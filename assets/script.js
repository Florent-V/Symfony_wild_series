console.log('ok');
document.getElementById('watchlist').addEventListener('click', addToWatchlist);

function addToWatchlist(event) {
    console.log('test')
    event.preventDefault();
    console.log('Hello Watchlist !!!');
}

console.log('ok2')