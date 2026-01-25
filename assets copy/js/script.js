// assets/js/script.js

// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Confirm delete actions
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Add to cart animation
function addToCartAnimation(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = '<i class="bi bi-check"></i> Ditambahkan!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1500);
    }, 800);
}

// Format currency input
function formatRupiah(angka, prefix = 'Rp ') {
    const number_string = angka.replace(/[^,\d]/g, '').toString();
    const split = number_string.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix + rupiah;
}

// Update cart quantity
function updateCartQuantity(id, action) {
    const qtyElement = document.getElementById('qty-' + id);
    let currentQty = parseInt(qtyElement.value);
    
    if (action === 'increase') {
        currentQty++;
    } else if (action === 'decrease' && currentQty > 1) {
        currentQty--;
    }
    
    qtyElement.value = currentQty;
}

// Image preview before upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}