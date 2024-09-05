<script setup>
import { RouterLink } from 'vue-router';
import logo from "@/assets/qr-logo.png";
import people from "@/assets/people.png";
import Input from '@/components/Input.vue';


</script>

<template>
<div class="bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-2 h-auto">
        <div class="mx-6 mt-6">
            <RouterLink :to="{name: 'home'}">
                <i class="pi pi-arrow-left text-secondary"></i>
            </RouterLink>
            
            <div class="flex flex-col items-center mt-2">
                <div>
                    <img :src="logo" alt="QuestRetrieve Logo" width="250px">
                </div>

                <div class="container mx-auto w-auto mt-14">
                    <form action="">
                        <div>
                            <div class="mb-5">
                                <h1 class="text-primary font-medium text-lg">Create your account</h1>
                            </div>
                            <div class="flex flex-row gap-5">

                            <div>
                                <label class="text-primary text-sm font-medium">Last Name</label>
                                <Input  />
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">First Name</label>
                                <Input  />
                            </div>
                        </div>

                            <div>
                                <label class="text-primary text-sm font-medium">Contact Number</label>
                                <Input  />
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Date of birth</label>
                                <br>
                                <input type="date" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded"  />
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Email</label>
                                <Input  />
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Password</label>
                                <Input  />
                            </div>
                            <div class="border-t-[1.1px] border-gray w-full mt-4"></div>
                            <div class="mt-4 flex flex-row items-center gap-1">
                                <h1 class="text-primary">Already have an account?</h1> 
                                <RouterLink :to="{name: 'login'}">
                                    <div class="underline text-primary"> Log-in</div>
                                </RouterLink>
                            </div>
                            <button class="bg-secondary rounded-lg px-6 py-1 text-white mt-5 text-sm">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="hidden lg:block bg-primary text-white">
            <div class="h-screen flex flex-col items-center justify-between">
                <div class="text-center mt-5">
                    <h1 class="text-lg font-bold">Connect with your community to recover lost items or return found ones.</h1>
                </div>
                <img :src="people" alt="Illustration of people">

                <p  class="w-auto mb-10">Simple, fast, and effective.</p>

                <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f3f4f5" fill-opacity="1" d="M0,256L0,128L120,128L120,192L240,192L240,96L360,96L360,32L480,32L480,192L600,192L600,224L720,224L720,256L840,256L840,0L960,0L960,160L1080,160L1080,192L1200,192L1200,64L1320,64L1320,192L1440,192L1440,320L1320,320L1320,320L1200,320L1200,320L1080,320L1080,320L960,320L960,320L840,320L840,320L720,320L720,320L600,320L600,320L480,320L480,320L360,320L360,320L240,320L240,320L120,320L120,320L0,320L0,320Z"></path></svg> -->
            </div>
        </div>
    </div>
</div>
</template>
<style scope>

</style>