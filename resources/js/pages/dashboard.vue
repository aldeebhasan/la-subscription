<script setup>

</script>

<template>

    <div class="container flex flex-wrap p-2">
        <div class="w-full text-center p-10">
            <h1 class="text-4xl font-extrabold  bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-300">
                La- Subscription
            </h1>
        </div>
        <div class="basis-1/2  p-1">
            <div class="dashboard-item">
                <div class="text-lg">Plans</div>
                <div>
                    <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> 50</span>
                </div>
            </div>
        </div>
        <div class="basis-1/2 p-1">
            <div class="dashboard-item">
                <div class="text-lg">Plugins</div>
                <div>
                    <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> 50</span>
                </div>
            </div>
        </div>

        <div class="basis-1/2  p-1">
            <div class="dashboard-item">
                <div class="text-lg">Features</div>
                <div>
                    <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> 50</span>
                </div>
            </div>
        </div>

        <div class="basis-1/2  p-1">
            <div class="dashboard-item">
                <div class="text-lg">Subscriptions</div>
                <div>
                    <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> 50</span>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

.dashboard-item {
    @apply bg-gray-200 border-2 p-2 flex justify-between items-center;
}
</style>
