<template>
    <div class="flex items-center shadow justify-between p-4 bg-white rounded-md">
        <div>
            <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase">
                {{ title }}
            </h6>
            <span class="text-xl font-semibold">{{ value }}</span>
        </div>
    </div>

</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        value: {
            type: [String, Number],
            required: true
        }
    }
}
</script>