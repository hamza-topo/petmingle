require('./bootstrap');
Echo.channel(`new-match`)
    .listen('.new.match', (e) => {
        console.log(e);
    });
Echo.channel(`new-message`)
    .listen('.new.message', (e) => {
        console.log(e);
    });