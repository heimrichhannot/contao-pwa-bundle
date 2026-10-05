// A clicked notification has to reach the installed app. openWindow() always
// creates a new browsing context, so on Android it hands the link to the browser
// while the installed app sits in the background; an open same-origin client has
// to be navigated and focused instead.
const assert = require('node:assert/strict')
const fs = require('node:fs')
const path = require('node:path')
const vm = require('node:vm')

const source = fs.readFileSync(path.join(__dirname, '../../public/frontend/huh-pwa-serviceworker.js'), 'utf8')
const context = vm.createContext({
    URL, console,
    self: { location: { origin: 'https://example.org' } },
})
vm.runInContext(source + '\nglobalThis.HuhPwaServiceWorker = HuhPwaServiceWorker', context)

// `windows` describes the open clients: [url, {navigate}] pairs.
function run(windows, { startUrl = '/', jumpTo } = {}) {
    const calls = { opened: [], navigated: [], focused: [] }
    const clients = {
        matchAll: async options => {
            // The worker runs in its own realm, so compare fields, not objects.
            assert.equal(options.type, 'window')
            assert.equal(options.includeUncontrolled, true)
            return windows.map(([url, navigate = 'ok']) => {
                const client = {
                    url,
                    focus: async () => { calls.focused.push(client.url); return client },
                }
                if (navigate === 'ok') {
                    client.navigate = async target => { calls.navigated.push(target); client.url = target; return client }
                } else if (navigate === 'throws') {
                    client.navigate = async () => { throw new Error('not controlled') }
                } else if (navigate === 'unfocusable') {
                    client.navigate = async () => { throw new Error('not controlled') }
                    client.focus = async () => { throw new Error('cannot focus') }
                }
                return client
            })
        },
        openWindow: async url => { calls.opened.push(url); return { url } },
    }
    const sw = new context.HuhPwaServiceWorker()
    sw.startUrl = startUrl
    const event = { type: 'notificationclick', notification: { data: jumpTo === undefined ? {} : { clickJumpTo: jumpTo } } }
    return sw.notificationClickEvent(event, clients).then(result => ({ calls, result }))
}

const target = 'https://example.org/chat/uuid'

async function verify() {
    // The app is open on another page: navigate it rather than opening a window.
    let { calls } = await run([['https://example.org/']], { jumpTo: target })
    assert.deepEqual(calls.navigated, [target], 'the open app is navigated')
    assert.deepEqual(calls.focused, [target], 'and focused')
    assert.deepEqual(calls.opened, [], 'no second window is opened')

    // Nothing open: a new window is the only way.
    ;({ calls } = await run([], { jumpTo: target }))
    assert.deepEqual(calls.opened, [target])

    // A window of another origin is not ours to reuse.
    ;({ calls } = await run([['https://other.example/']], { jumpTo: target }))
    assert.deepEqual(calls.opened, [target])
    assert.deepEqual(calls.navigated, [])

    // Already on the target page: focus it, do not navigate it again.
    ;({ calls } = await run([[target]], { jumpTo: target }))
    assert.deepEqual(calls.navigated, [], 'the current page is not reloaded')
    assert.deepEqual(calls.focused, [target])

    // An uncontrolled client rejects navigate(). Opening a window instead would be
    // ignored by an app that is already running, so the app is focused anyway.
    ;({ calls } = await run([['https://example.org/', 'throws']], { jumpTo: target }))
    assert.deepEqual(calls.focused, ['https://example.org/'], 'the app is brought forward')
    assert.deepEqual(calls.opened, [], 'and no window is opened on top of it')

    // A client that cannot even be focused must not swallow the click.
    ;({ calls } = await run([['https://example.org/', 'unfocusable']], { jumpTo: target }))
    assert.deepEqual(calls.opened, [target], 'a window opens when the client is unusable')

    // Without a target the click still has to reach the app.
    ;({ calls } = await run([], { startUrl: '/' }))
    assert.deepEqual(calls.opened, ['https://example.org/'], 'the start URL opens')

    const nowhere = await run([], { startUrl: '' })
    assert.equal(nowhere.result, null)
    assert.deepEqual(nowhere.calls.opened, [], 'no start URL means no window')

    // A relative target resolves against the worker's own origin.
    ;({ calls } = await run([], { jumpTo: '/chat/uuid' }))
    assert.deepEqual(calls.opened, [target])

    console.log('PASS: a clicked notification reuses the open app and only opens a window when there is none')
}
verify().catch(error => { console.error(error); process.exitCode = 1 })
