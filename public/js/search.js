const searchBar = document.getElementById("search-bar");
const searchResults = document.getElementById("search-results");


searchBar.addEventListener("input", async function() {
    let value = searchBar.value;
    searchResults.innerHTML = "";
    if (value === "") {
        return;
    }
    let response = await fetch(`api/search.php?q=${value}`);
    let data = await response.json();


    data.forEach(song => {
        let div = document.createElement('div')
        div.className = 'search-result-item';

        let link = document.createElement('a');
        link.href = `/songs/${song.slug}`;
        let text = song.title_romaji;
        if (song.title_ja) {
        text += ` - (${song.title_ja})`;
        }
        if (song.title_en && song.title_en !== song.title_romaji) {
        text += ` - ${song.title_en}`;
        }
    
    link.textContent = text;
    div.appendChild(link);
    div.appendChild(document.createElement('hr'))
    searchResults.appendChild(div);
        
    });
})