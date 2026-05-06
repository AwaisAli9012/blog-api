<script setup>
defineProps({
    show: Boolean,
    title: { type: String, default: 'Are you sure?' },
    message: { type: String, default: 'This action cannot be undone.' },
    confirmText: { type: String, default: 'Confirm' },
    confirmColor: { type: String, default: '#ef4444' },
    icon: { type: String, default: '⚠️' },
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
    <Teleport to="body">
        <div v-if="show" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(4px)">
            <div style="background:white;border-radius:20px;padding:32px;max-width:420px;width:90%;box-shadow:0 25px 60px rgba(0,0,0,0.3);text-align:center">
                <div style="font-size:3rem;margin-bottom:16px">{{ icon }}</div>
                <h2 style="font-size:1.4rem;font-weight:800;color:#1a1a2e;margin:0 0 10px">{{ title }}</h2>
                <p style="color:#6b7280;margin:0 0 28px;font-size:0.95rem;line-height:1.6">{{ message }}</p>
                <div style="display:flex;gap:12px;justify-content:center">
                    <button @click="$emit('cancel')" style="padding:10px 28px;border-radius:10px;border:2px solid #e5e7eb;background:white;color:#374151;font-weight:600;cursor:pointer;font-size:0.95rem">
                        Cancel
                    </button>
                    <button @click="$emit('confirm')" :style="`padding:10px 28px;border-radius:10px;border:none;background:${confirmColor};color:white;font-weight:600;cursor:pointer;font-size:0.95rem`">
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
