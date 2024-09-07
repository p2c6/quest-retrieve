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
                                <h2 class="font-bold">Create Subcategory</h2>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-primary text-sm">Category</label>
                            <div>
                                <select class="h-10 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-primary text-sm">Name</label>
                            <Input />
                        </div>

                        <button class="w-full bg-secondary p-1 mt-2 text-white rounded">Submit</button>
                        
                    </form>
            </Card>
        </div>
</div>

</template>