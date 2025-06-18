document.getElementById('perpage').addEventListener('change', change);
document.getElementById('search').addEventListener('keydown',  function(event) {
    if (event.key === 'Enter') {
        Search();
    }
});

function change() {
    const url=new URL(window.location.href);    
    const perPage = document.getElementById('perpage').value;
    url.searchParams.set('perpage', perPage);
    window.location.href = url.toString();
}


function PrevPage() {
    const url = new URL(window.location.href);
    const currentPage = parseInt(url.searchParams.get('page') || '1');
    if (currentPage > 1) {
        url.searchParams.set('page', currentPage - 1);
        window.location.href = url.toString();
    }
}

function NextPage(maxPage) {
    const url = new URL(window.location.href);
    const currentPage = parseInt(url.searchParams.get('page') || '1');
    if (currentPage < maxPage) {
        url.searchParams.set('page', currentPage + 1);
        window.location.href = url.toString();
    }
}

function Search() {
    const url = new URL(window.location.href);
    const searchValue = document.getElementById('search').value;
    url.searchParams.set('search', searchValue);
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}