document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.href;
    const followerTab = document.querySelector('a[href*="page=follower"]');
    const followingTab = document.querySelector('a[href*="page=following"]');
    
    if (currentUrl.includes('page=follower')) {
        followerTab.classList.add('text-black');
        followerTab.classList.add('dark:text-white');
        followerTab.querySelector('.text-tertiary-500').classList.remove('text-tertiary-500');
    
    } else if (currentUrl.includes('page=following')) {
        followingTab.classList.add('text-black');
        followingTab.classList.add('dark:text-white');
        followingTab.querySelector('.text-tertiary-500').classList.remove('text-tertiary-500');
    }
});