import './bootstrap';
// Import pustaka html5-qrcode
import { Html5QrcodeScanner } from "html5-qrcode";
import Alpine from 'alpinejs';
import { Fancybox } from "@fancyapps/ui";

window.Alpine = Alpine;

Alpine.start();


// ===== Kod Pengimbas QR (Dibalut dengan IF) =====
if (document.getElementById('qr-reader')) {

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        document.getElementById('qr-reader-results').innerText = `Mengimbas: ${decodedText}`;

        // Hentikan pengimbas dahulu untuk elak imbasan berganda semasa proses
        if (window.html5QrCodeScanner) {
            window.html5QrCodeScanner.clear().catch(error => console.error("Gagal henti scanner.", error));
        }

        // Semak jika ia URL item kita (Kod QR) atau kod bar lain
        let itemIdFromUrl = null;
        try {
            // Cuba parse sebagai URL dan cari /items/ID
            const url = new URL(decodedText);
            const pathSegments = url.pathname.split('/');
            // Cari segmen numerik terakhir selepas 'items'
            let potentialId = pathSegments.pop() || pathSegments.pop();
            if (pathSegments.includes('items') && potentialId && /^\d+$/.test(potentialId)) {
                itemIdFromUrl = potentialId;
            }
        } catch (e) {
            // Bukan URL yang sah, anggap ia kod bar biasa
            // console.log("Bukan URL, anggap kod bar.");
        }

        if (itemIdFromUrl) {
            // --- Logik Kod QR Diubahsuai ---
            console.log(`QR Code Item ID found: ${itemIdFromUrl}`);
            document.getElementById('qr-reader-results').innerText = `Kod QR Item ID ${itemIdFromUrl} ditemui. Menyemak kebenaran...`;

            // Buat panggilan POST ke backend untuk semak akses & dapatkan URL pergerakan
            // Guna route baru '/qr-item-check/{item}' yang akan kita cipta
            axios.post(`/qr-item-check/${itemIdFromUrl}`) // Hantar POST ke route dengan ID item
            .then(function (response) {
                console.log('QR Check Response:', response.data);
                if (response.data.status === 'allowed' && response.data.move_url) {
                    // Jika dibenarkan, redirect ke URL borang pergerakan
                    document.getElementById('qr-reader-results').innerText = `Akses dibenarkan. Mengalih ke borang pergerakan...`;
                    window.location.href = response.data.move_url;
                } else {
                    // Jika tidak dibenarkan (atau ralat lain dari backend)
                    let message = response.data.message || 'Anda tidak dibenarkan mengakses item ini.';
                    document.getElementById('qr-reader-results').innerText = `Ralat: ${message}`;
                    alert(`Ralat: ${message}`);
                    // Mungkin mulakan semula scanner jika perlu
                }
            })
            .catch(function (error) {
                // Ralat semasa membuat permintaan AJAX
                console.error("Error during QR item check:", error);
                document.getElementById('qr-reader-results').innerText = 'Ralat semasa menyemak item Kod QR.';
                alert('Ralat semasa menyemak item Kod QR. Sila cuba lagi.');
                // Mungkin mulakan semula scanner
            });

        } else {
            // --- Logik Kod Bar Produk (Sedia Ada) ---
            console.log(`Product barcode scanned: ${decodedText}`);
            document.getElementById('qr-reader-results').innerText = `Kod Bar ${decodedText} ditemui. Menyemak pangkalan data...`;

            axios.post('/barcode-lookup', { barcode: decodedText })
            .then(function (response) {
                console.log('Lookup Response:', response.data);
                if (response.data.status === 'found' && response.data.show_url) {
                    document.getElementById('qr-reader-results').innerText = `Item ditemui! Mengalih...`;
                    window.location.href = response.data.show_url;
                } else if (response.data.status === 'not_found' && response.data.create_url) {
                    document.getElementById('qr-reader-results').innerText = `Item tidak ditemui. Mengalih ke borang tambah...`;
                    window.location.href = response.data.create_url;
                } else {
                    document.getElementById('qr-reader-results').innerText = 'Ralat: Respons tidak dijangka dari server.';
                    alert('Ralat: Respons tidak dijangka dari server.');
                }
            })
            .catch(function (error) {
                console.error("Error during barcode lookup:", error);
                document.getElementById('qr-reader-results').innerText = 'Ralat semasa menyemak kod bar.';
                alert('Ralat semasa menyemak kod bar. Sila cuba lagi.');
            });
        }
    }

    function onScanFailure(error) {
        // console.warn(`QR error = ${error}`);
    }

    // Cipta instance & render
    // Simpan instance ke window scope supaya boleh diakses dalam onScanSuccess
    window.html5QrCodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        {
            fps: 10,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                    let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdge * 0.7);
                    qrboxSize = Math.max(50, qrboxSize);
                    return { width: qrboxSize, height: qrboxSize };
                }
        },
       false); // verbose = false

    html5QrCodeScanner.render(onScanSuccess, onScanFailure);

} // Tutup blok 'if'
// =============================================

/**
 * Fungsian untuk menjadikan baris jadual item boleh diklik.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cari badan jadual item menggunakan ID yang kita tambah tadi
    const tableBody = document.querySelector('#item-table-body');

    // Di dalam atau selepas DOMContentLoaded
    Fancybox.bind("[data-fancybox]", {
        // Opsyen konfigurasi tambahan boleh diletak di sini jika perlu
        // Contoh: loop: true,
    });


    // Hanya jalankan jika elemen badan jadual wujud (iaitu di halaman senarai item)
    if (tableBody) {
        tableBody.addEventListener('click', function(event) {
            // Cari elemen <tr> terdekat yang mempunyai kelas clickable-row
            const row = event.target.closest('tr.clickable-row');

            // Pastikan kita jumpa baris DAN klik itu BUKAN pada pautan, butang, borang, atau input di dalam baris itu
            if (row && !event.target.closest('a, button, form, input, select, .form-check-input, .form-check-label')) {
                // Dapatkan URL dari atribut data-href
                const href = row.dataset.href;

                // Jika URL wujud, pergi ke URL tersebut
                if (href) {
                    window.location.href = href;
                }
            }
            // Jika klik berlaku pada butang/pautan dalam baris, jangan buat apa-apa (biarkan tindakan asal berjalan)
        });
    }
});

let deferredInstallPrompt = null;
const installPwaBanner = document.getElementById('installPwaBanner'); // Sasar banner div
const installButton = document.getElementById('installPwaButton');
const dismissButton = document.getElementById('dismissPwaBanner'); // Butang tutup banner

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    if (installPwaBanner) {
        console.log('PWA install prompt event deferred. Showing banner.');
        installPwaBanner.style.display = 'flex'; // Guna flex untuk susun item dalam alert
        installPwaBanner.classList.add('justify-content-between', 'align-items-center'); // Untuk susunan kemas
    }
});

if (installButton) {
    installButton.addEventListener('click', async () => {
        if (!deferredInstallPrompt) {
            console.log('Deferred install prompt not available.');
            return;
        }
        deferredInstallPrompt.prompt();
        const { outcome } = await deferredInstallPrompt.userChoice;
        console.log(`User choice: ${outcome}`);
        deferredInstallPrompt = null;
        if (installPwaBanner) {
            installPwaBanner.style.display = 'none'; // Sembunyikan banner selepas prompt
        }
    });
}

if (dismissButton) {
    dismissButton.addEventListener('click', () => {
        if (installPwaBanner) {
            installPwaBanner.style.display = 'none'; // Sembunyikan banner jika pengguna tutup manual
            console.log('PWA install banner dismissed by user.');
            // Mungkin Tuan mahu simpan status ini dalam localStorage supaya tidak papar lagi? (Lanjutan)
        }
    });
}

window.addEventListener('appinstalled', (event) => {
    console.log('InventoriKu PWA installed successfully!');
    deferredInstallPrompt = null; // Pastikan prompt direset
    if (installPwaBanner) {
        installPwaBanner.style.display = 'none'; // Sembunyikan banner selepas berjaya pasang
    }
});
