/*
 * Dokumentasi file: Modul JavaScript frontend.
 *
 * Menjelaskan tanggung jawab file resources/js/app.js serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */
import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.Chart = Chart;
window.ApexCharts = ApexCharts;

Alpine.start();
