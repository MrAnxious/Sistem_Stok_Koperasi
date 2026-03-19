<!-- KOMPONEN NOTIFIKASI GLOBAL -->
<div class="relative" x-data="{ 
    openNotif: false, 
    notifCount: 0, 
    notifications: [],
    
    async fetchNotifications() {
        try {
            const response = await fetch('../../proses/get_notifikasi.php');
            const result = await response.json();
            if(result.sukses) {
                this.notifCount = result.count;
                this.notifications = result.data;
            }
        } catch(e) {
            console.error('Gagal mengambil notifikasi', e);
        }
    }
}" x-init="fetchNotifications()">

    <!-- Ikon Bel -->
    <button @click="openNotif = !openNotif" @click.away="openNotif = false" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors mt-2">
        <span class="material-symbols-outlined">notifications</span>
        <!-- Bintik Merah Indikator -->
        <span x-show="notifCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-600 rounded-full border-2 border-white flex justify-center items-center text-[9px] text-white font-bold" x-text="notifCount > 9 ? '9+' : notifCount" style="display: none;"></span>
    </button>
    
    <!-- Dropdown Notifikasi -->
    <div x-show="openNotif" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-[100]" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Notifikasi</h3>
            <span class="text-xs bg-primary/10 text-primary px-2 py-1 rounded-md font-semibold" x-text="notifCount + ' Baru'"></span>
        </div>
        
        <div class="max-h-[350px] overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="p-8 text-center text-gray-500 text-sm">
                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2 block mx-auto">notifications_paused</span>
                    <p class="text-sm">Belum ada notifikasi baru hari ini.</p>
                </div>
            </template>
            
            <template x-for="item in notifications" :key="item.pesan + item.waktu">
                <a :href="item.link" class="block p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <div class="flex gap-3">
                        <div :class="'w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center ' + item.bg + ' ' + item.warna">
                            <span class="material-symbols-outlined text-sm" x-text="item.icon"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900" x-text="item.judul"></h4>
                            <p class="text-xs text-gray-600 mt-0.5" x-text="item.pesan"></p>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium" x-text="item.waktu"></p>
                        </div>
                    </div>
                </a>
            </template>
        </div>
        
        <div class="p-3 border-t border-gray-100 bg-gray-50 text-center">
            <a href="log_aktivitas.php" class="text-xs font-semibold text-primary hover:text-red-800">Lihat Semua Aktivitas</a>
        </div>
    </div>
</div>
