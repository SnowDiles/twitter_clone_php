document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.href;
    const followerTabs = document.querySelectorAll('a[href*="page=follower"] span');
    const followingTabs = document.querySelectorAll('a[href*="page=following"] span');

    if (currentUrl.includes('page=follower')) {
        followerTabs.forEach(tab => {
            tab.classList.add('text-black');
            tab.classList.add('dark:text-white');

        });
    } else if (currentUrl.includes('page=following')) {
        followingTabs.forEach(tab => {
            tab.classList.add('text-black');
            tab.classList.add('dark:text-white');

        });
    }
});