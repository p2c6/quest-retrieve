<script setup>
defineProps({
    title: String,
    description: String,
    isVisible: Boolean,
})

const emit = defineEmits(['closeDialogBox', 'confirm'])

const handleCloseDialogBox = () => {
    emit('closeDialogBox')
}

const handleConfirm = () => {
    emit('confirm')
}

</script>

<template>
    <Teleport to="#modal-container">
        <div :class="`fixed inset-0 z-20 flex items-center justify-center ${isVisible ? 'block' : 'hidden'}`">
            <div class="absolute inset-0 bg-slate-950 opacity-40" @click="closeModal"></div>
            <div id="modal-card" class="relative w-80 h-40 bg-white z-20 rounded-2xl">
                <div id="modal-header" class="w-full py-2">
                    <div class="flex justify-end mr-5">
                        <i class="pi pi-times text-gray-500 cursor-pointer" @click="handleCloseDialogBox"></i>
                    </div>

                </div>
                <div id="modal-body" class="flex items-center justify-center px-4">
                    <div class="flex-1">
                        <div class="flex flex-col justify-center gap-2">
                            <p class="font-semibold text-md text-primary">{{ title }}</p>
                            <p class="text-xs text-gray-400">{{ description }}</p>
                            <div class="flex gap-1 mt-2">
                                <button class="w-36 h-8 bg-gray-200 rounded text-sm" @click="handleCloseDialogBox">Cancel</button>
                                <button class="w-36 h-8 bg-secondary rounded text-white text-sm" @click="handleConfirm">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </Teleport>
</template>