<script setup>
import Input from '@/components/Input.vue';
import Card from '@/components/Card.vue';


</script>

<template>
    <div class="mt-5 md:mt-5">
        <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[420px]">
            <Card class="p-8">
                    <form action="">
                        <div class="border-b-2 mb-5">
                            <div class="flex flex-row items-center justify-center p-2 gap-x-2 text-primary">
                                <i class="pi pi-id-card text-secondary"></i>
                                <h2 class="font-bold">Claim</h2>
                            </div>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Claimer's name</label>
                            <Input />
                        </div>
                        <div>
                            <label class="text-primary text-sm">Please describe the item lost as much as possible in a very detailed way</label>
                            <Input />
                        </div>

                        <div>
                            <label class="text-primary text-sm">Claimer's name</label>
                            <Input />
                        </div>

                        <div>
                            <label class="text-primary text-sm">Claimer's name</label>
                            <Input />
                        </div>
                       

                        <button class="w-full bg-secondary p-1 mt-2 text-white rounded">Submit</button>
                        
                        
                    </form>
            </Card>
        </div>
</div>

</template>