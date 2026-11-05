import Alpine from 'alpinejs';

// LocalStorage Compare Store with safety try/catch
const getInitialCompare = () => {
    try {
        const stored = localStorage.getItem('spk_compare_laptops');
        return stored ? JSON.parse(stored) : [];
    } catch (e) {
        return [];
    }
};

Alpine.store('toast', {
    message: '',
    type: 'info',
    visible: false,
    timer: null,

    show(msg, type = 'info') {
        this.message = msg;
        this.type = type;
        this.visible = true;
        if (this.timer) clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            this.visible = false;
        }, 3000);
    }
});

Alpine.store('theme', {
    mode: (function() {
        try {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || saved === 'light') return saved;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        } catch (e) {
            return 'light';
        }
    })(),

    init() {
        this.apply(false);
        try {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    this.mode = e.matches ? 'dark' : 'light';
                    this.apply(true);
                }
            });
        } catch (e) {}
    },

    toggle() {
        this.mode = this.mode === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem('theme', this.mode);
        } catch (e) {}
        this.apply(true);
        if (Alpine.store('toast')) {
            Alpine.store('toast').show(
                this.mode === 'dark' ? 'Mode Gelap diaktifkan' : 'Mode Terang diaktifkan',
                'info'
            );
        }
    },

    apply(withAnimation = true) {
        if (withAnimation) {
            document.documentElement.classList.add('theme-transition');
            setTimeout(() => {
                document.documentElement.classList.remove('theme-transition');
            }, 400);
        }

        if (this.mode === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    get isDark() {
        return this.mode === 'dark';
    }
});

Alpine.store('compare', {
    items: getInitialCompare(),

    has(id) {
        return this.items.some(item => Number(item.id) === Number(id));
    },

    toggle(laptop) {
        const index = this.items.findIndex(item => Number(item.id) === Number(laptop.id));
        if (index > -1) {
            this.items.splice(index, 1);
            Alpine.store('toast').show(laptop.name + ' dihapus dari perbandingan.', 'info');
        } else {
            if (this.items.length >= 3) {
                Alpine.store('toast').show('Maksimal 3 laptop untuk dibandingkan sekaligus.', 'warning');
                return;
            }
            this.items.push({
                id: Number(laptop.id),
                name: laptop.name,
                brand: laptop.brand,
                image: laptop.image,
                price: laptop.price
            });
            Alpine.store('toast').show(laptop.name + ' ditambahkan ke perbandingan (' + this.items.length + '/3).', 'success');
        }
        this.save();
    },

    add(laptop) {
        if (this.has(laptop.id)) return;
        if (this.items.length >= 3) {
            Alpine.store('toast').show('Maksimal 3 laptop untuk dibandingkan sekaligus.', 'warning');
            return;
        }
        this.items.push({
            id: Number(laptop.id),
            name: laptop.name,
            brand: laptop.brand,
            image: laptop.image,
            price: laptop.price
        });
        Alpine.store('toast').show(laptop.name + ' ditambahkan ke perbandingan (' + this.items.length + '/3).', 'success');
        this.save();
    },

    remove(id) {
        const removed = this.items.find(item => Number(item.id) === Number(id));
        this.items = this.items.filter(item => Number(item.id) !== Number(id));
        if (removed) {
            Alpine.store('toast').show(removed.name + ' dihapus dari perbandingan.', 'info');
        }
        this.save();
    },

    clear() {
        this.items = [];
        this.save();
        Alpine.store('toast').show('Daftar perbandingan dikosongkan.', 'info');
    },

    save() {
        try {
            localStorage.setItem('spk_compare_laptops', JSON.stringify(this.items));
        } catch (e) {}
    },

    get count() {
        return this.items.length;
    },

    get compareUrl() {
        if (this.items.length === 0) return '/bandingkan';
        const params = this.items.map(i => `laptop_ids[]=${i.id}`).join('&');
        return `/bandingkan?${params}`;
    }
});

window.Alpine = Alpine;
Alpine.start();

