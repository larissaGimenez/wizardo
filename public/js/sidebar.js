document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const mobileToggle = document.getElementById('mobile-toggle');

    // Desktop Toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Mobile Toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            sidebar.classList.remove('collapsed');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickInsideMobileBtn = mobileToggle ? mobileToggle.contains(event.target) : false;
        const isClickInsideToggleBtn = toggleBtn ? toggleBtn.contains(event.target) : false;

        if (!isClickInsideSidebar && !isClickInsideMobileBtn && !isClickInsideToggleBtn && window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }
    });

    // Adjust on resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
        }
    });
});
