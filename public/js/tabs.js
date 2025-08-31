document.addEventListener('DOMContentLoaded', function() {
    // Get all tab buttons and content
    const tabs = document.querySelectorAll('.farayez-tab');
    
    function activateTab(selectedTab) {
        // Remove active state from all tabs
        tabs.forEach(tab => {
            tab.classList.remove('active');
            tab.classList.add('border-transparent');
            tab.classList.remove('border-[#41AB5D]');
        });

        // Add active state to selected tab
        selectedTab.classList.add('active');
        selectedTab.classList.remove('border-transparent');
        selectedTab.classList.add('border-[#41AB5D]');

        // Handle content visibility
        const tabId = selectedTab.getAttribute('data-tab');
        document.querySelectorAll('.farayez-content').forEach(content => {
            content.classList.add('hidden');
        });
        const content = document.getElementById(tabId);
        if (content) {
            content.classList.remove('hidden');
        }
    }

    // Add click event to each tab
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            activateTab(tab);
        });
    });
});
