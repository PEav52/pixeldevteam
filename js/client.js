document.addEventListener('DOMContentLoaded', function() {
    // Record start time
    const startTime = performance.now();
    
    // Track page visit
    const pageUrl = window.location.pathname + window.location.search;
    fetch('./admin_dashboard/api/visitor_tracker.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ page_url: pageUrl })
    })
    .then(response => response.json())
    .then(data => {
        if (data.session_id && data.visitor_id) {
            // Store session_id and visitor_id in cookies
            document.cookie = `tracker_session_id=${data.session_id}; path=/; max-age=3600`;
            document.cookie = `tracker_visitor_id=${data.visitor_id}; path=/; max-age=3600`;
        }
    })
    .catch(error => console.error('Tracking error:', error));

    // Track duration on page exit
    window.addEventListener('beforeunload', function() {
        const duration = Math.round((performance.now() - startTime) / 1000); // Duration in seconds
        const sessionId = document.cookie.match(/tracker_session_id=([^;]+)/)?.[1];
        const visitorId = document.cookie.match(/tracker_visitor_id=([^;]+)/)?.[1];
        
        if (sessionId && visitorId) {
            fetch(`./admin_dashboard/api/visitor_tracker.php?session_id=${sessionId}&visitor_id=${visitorId}&visit_duration=${duration}`, {
                method: 'PUT'
            });
        }
    });
});
