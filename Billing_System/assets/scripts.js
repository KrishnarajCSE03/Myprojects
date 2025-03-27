document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById("revenueChart").getContext("2d");
    var revenueChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            datasets: [{
                label: "Revenue ($)",
                data: [5000, 7000, 8000, 10000, 12000, 15000],
                backgroundColor: "rgba(42, 82, 152, 0.7)"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var sidebar = document.querySelector(".sidebar");
    var toggleBtn = document.createElement("button");
    toggleBtn.innerHTML = "☰";
    toggleBtn.classList.add("toggle-btn");
    document.body.appendChild(toggleBtn);

    toggleBtn.addEventListener("click", function() {
        sidebar.classList.toggle("collapsed");
        document.querySelector(".main-content").classList.toggle("expanded");
    });
});

