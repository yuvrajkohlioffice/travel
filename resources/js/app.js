// Import and set up Chart.js globally
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import Turbo from Hotwired
import * as Turbo from "@hotwired/turbo";
window.Turbo = Turbo;

// Optional: Turbo configuration
Turbo.session.drive = true; // Enables Turbo navigation (default is true)
