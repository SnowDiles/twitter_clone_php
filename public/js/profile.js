document.addEventListener('DOMContentLoaded', function() {
    function updateTabsStyle(page) {
        const tabs = document.querySelectorAll(`a[href*="page=${page}"] span`);
        tabs.forEach(tab => {
            tab.classList.add('text-black', 'dark:text-white');
        });
    }

    const currentUrl = window.location.href;
    if (currentUrl.includes('page=follower')) {
        updateTabsStyle('follower');
    } else if (currentUrl.includes('page=following')) {
        updateTabsStyle('following');
    }
});
