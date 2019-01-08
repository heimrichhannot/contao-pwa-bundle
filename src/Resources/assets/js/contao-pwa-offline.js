// A test about how to get cached offline pages

caches.open('cache-sw-deutsch-20190108113235').then(function(cache) {
    cache.keys().then(function(names) {
        let paths = [];
        names.forEach(function(name) {
            let url = new URL(name.url);
            if (url.pathname.match('^\/de') && !url.pathname.match('^\/de/offline')) {
                paths.push(url.pathname);
            }
        });
        console.log(paths);
    });
});