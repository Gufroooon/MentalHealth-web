/*
 * Dokumentasi file: Modul JavaScript frontend.
 *
 * Menjelaskan tanggung jawab file resources/js/bootstrap.js serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
