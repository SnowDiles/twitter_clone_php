export class trendingHashtags {
  constructor(fetchUrl, idInsert) {
    this.fetchUrl = fetchUrl;
    this.idInsert = idInsert;
  }

  init() {
    this.insertData();
  }

  async insertData() {
    const hashtags = await this.fetchData();
    const container = document.getElementById(this.idInsert);
    container.innerHTML = '';

    if (hashtags.length > 0) {
      const trendsTitle = document.createElement("p");
      trendsTitle.className = "text-gray-400 text-sm dark:text-gray-400";
      trendsTitle.textContent = "Tendances";
      container.appendChild(trendsTitle);

      hashtags.forEach((element) => {
        const hashtagElement = document.createElement("li");
        hashtagElement.className = "pb-5 font-bold text-l dark:text-white";
        hashtagElement.innerHTML = `<a href="./SearchController.php?hashtag=${element.tag}" class="dark:text-blue-500">#${element.tag}</a>`;

        const publicationsElement = document.createElement("p");
        publicationsElement.className = "text-gray-400 text-sm dark:text-gray-400";
        publicationsElement.textContent = `${element.nbr_use} publications`;

        hashtagElement.appendChild(publicationsElement);
        container.appendChild(hashtagElement);
      });
    } else {
      const noHashtagElement = document.createElement("li");
      noHashtagElement.className = "text-gray-400 text-sm dark:text-gray-400";
      noHashtagElement.textContent = "Aucun hashtag";
      container.appendChild(noHashtagElement);
    }
  }

  async fetchData() {
    const formData = new FormData();
    formData.append("action", "trendingHashtags");

    const response = await fetch(this.fetchUrl, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    if (data.success) {
      return data.data.hashtags;
    } else {
      throw new Error("Error fetching data");
    }
  }
}
