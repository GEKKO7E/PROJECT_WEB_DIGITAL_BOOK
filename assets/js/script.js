document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const ebookCards = document.querySelectorAll('.ebook-card');
    const emptySearchMsg = document.getElementById('emptySearchMsg');

    const ebookSelect = document.getElementById('ebook_id');
    const memberTierSelect = document.getElementById('member_tier');
    const daysInput = document.getElementById('days');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const discountDisplay = document.getElementById('discountDisplay');
    const estimatedTotalEl = document.getElementById('estimatedTotal');
    const rentalForm = document.getElementById('rentalForm');

    // Modal elements
    const detailModal = document.getElementById('detailModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModalFooterBtn = document.getElementById('closeModalFooterBtn');
    const modalSelectBtn = document.getElementById('modalSelectBtn');
    let activeModalBookId = '';

    // 1. Filter & Pencarian Real-Time
    function filterCatalog() {
        const keyword = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;
        let visibleCount = 0;

        ebookCards.forEach(card => {
            const title = card.dataset.title || '';
            const author = card.dataset.author || '';
            const category = card.dataset.category || '';

            const matchesSearch = title.includes(keyword) || author.includes(keyword);
            const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

            const isVisible = matchesSearch && matchesCategory;
            card.style.display = isVisible ? 'flex' : 'none';
            if (isVisible) visibleCount++;
        });

        emptySearchMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterCatalog);
    categoryFilter.addEventListener('change', filterCatalog);

    function selectBookForRental(bookId) {
        ebookSelect.value = bookId;
        updateEstimatedCost();
        document.getElementById('sewa').scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('.select-ebook-btn').forEach(btn => {
        btn.addEventListener('click', () => selectBookForRental(btn.dataset.id));
    });

    document.querySelectorAll('.detail-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            activeModalBookId = btn.dataset.id;
            document.getElementById('modalCategory').textContent = btn.dataset.category;
            document.getElementById('modalTitle').textContent = btn.dataset.title;
            document.getElementById('modalAuthor').textContent = 'Penulis: ' + btn.dataset.author;
            document.getElementById('modalSynopsis').textContent = btn.dataset.synopsis;
            document.getElementById('modalFormat').textContent = btn.dataset.format;
            document.getElementById('modalPages').textContent = btn.dataset.pages + ' hlm';
            document.getElementById('modalRate').textContent = btn.dataset.rate;

            const optionExists = Array.from(ebookSelect.options).some(opt => opt.value === activeModalBookId);
            modalSelectBtn.style.display = optionExists ? 'inline-flex' : 'none';

            detailModal.classList.add('open');
            detailModal.setAttribute('aria-hidden', 'false');
        });
    });

    function closeModal() {
        detailModal.classList.remove('open');
        detailModal.setAttribute('aria-hidden', 'true');
    }

    closeModalBtn.addEventListener('click', closeModal);
    closeModalFooterBtn.addEventListener('click', closeModal);
    detailModal.addEventListener('click', (e) => {
        if (e.target === detailModal) closeModal();
    });

    modalSelectBtn.addEventListener('click', () => {
        closeModal();
        if (activeModalBookId) {
            selectBookForRental(activeModalBookId);
        }
    });

    function updateEstimatedCost() {
        const selectedOption = ebookSelect.options[ebookSelect.selectedIndex];
        const dailyRate = parseFloat(selectedOption?.dataset.rate || '0');
        const days = parseInt(daysInput.value || '0', 10);

        const tierOption = memberTierSelect.options[memberTierSelect.selectedIndex];
        const discountRate = parseFloat(tierOption?.dataset.discount || '0');

        if (dailyRate > 0 && days > 0) {
            const subtotal = dailyRate * days;
            const discountAmount = subtotal * discountRate;
            const finalTotal = subtotal - discountAmount;

            subtotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            discountDisplay.textContent = '- Rp ' + discountAmount.toLocaleString('id-ID');
            estimatedTotalEl.textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
        } else {
            subtotalDisplay.textContent = 'Rp 0';
            discountDisplay.textContent = '- Rp 0';
            estimatedTotalEl.textContent = 'Rp 0';
        }
    }

    ebookSelect.addEventListener('change', updateEstimatedCost);
    memberTierSelect.addEventListener('change', updateEstimatedCost);
    daysInput.addEventListener('input', updateEstimatedCost);

    rentalForm.addEventListener('submit', (e) => {
        let isValid = true;

        const nameInput = document.getElementById('borrower_name');
        const emailInput = document.getElementById('borrower_email');

        document.getElementById('nameError').textContent = '';
        document.getElementById('emailError').textContent = '';
        document.getElementById('ebookError').textContent = '';
        document.getElementById('daysError').textContent = '';

        if (nameInput.value.trim().length < 3) {
            document.getElementById('nameError').textContent = 'Nama peminjam minimal 3 karakter.';
            isValid = false;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value.trim())) {
            document.getElementById('emailError').textContent = 'Masukkan format email yang valid.';
            isValid = false;
        }

        if (!ebookSelect.value) {
            document.getElementById('ebookError').textContent = 'Silakan pilih e-book yang akan disewa.';
            isValid = false;
        }

        const daysVal = parseInt(daysInput.value, 10);
        if (isNaN(daysVal) || daysVal < 1 || daysVal > 30) {
            document.getElementById('daysError').textContent = 'Durasi peminjaman harus 1 sampai 30 hari.';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});