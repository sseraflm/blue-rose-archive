const searchBar = document.getElementById("search-bar");
const searchResults = document.getElementById("search-results");

const DEBOUNCE_DELAY = 200;

let debounceTimer = null;
let latestRequestId = 0;

function clearResults() {
  searchResults.innerHTML = "";
}

function renderResults(songs) {
  clearResults();

  songs.forEach((song) => {
    const item = document.createElement("div");
    item.className = "search-result-item";

    const link = document.createElement("a");
    link.href = `/songs/${song.slug}`;

    let text = song.title_romaji;
    if (song.title_ja) {
      text += ` - (${song.title_ja})`;
    }
    if (song.title_en && song.title_en !== song.title_romaji) {
      text += ` - ${song.title_en}`;
    }

    link.textContent = text;
    item.appendChild(link);
    item.appendChild(document.createElement("hr"));
    searchResults.appendChild(item);
  });
}

async function runSearch(query) {
  const requestId = ++latestRequestId;

  try {
    const response = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
    if (!response.ok) {
      throw new Error(`Search failed with status ${response.status}`);
    }
    const songs = await response.json();

    if (requestId !== latestRequestId) {
      return;
    }

    renderResults(songs);
  } catch (error) {
    console.error(error);
    if (requestId === latestRequestId) {
      clearResults();
    }
  }
}
searchBar.addEventListener("input", () => {
  clearTimeout(debounceTimer);

  const query = searchBar.value.trim();

  if (query === "") {
    latestRequestId++;
    clearResults();
    return;
  }

  debounceTimer = setTimeout(() => runSearch(query), DEBOUNCE_DELAY);
});