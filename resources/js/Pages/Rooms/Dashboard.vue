<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

defineProps({
    rooms: Array,
});

const canAccessRoom = ({ participants }) => {
    return participants.map(({ id }) => id).includes(page.props.auth.user.id);
};
</script>

<template>
    <AppLayout title="All Rooms">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                All Rooms
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <ul class="p-4">
                        <template v-for="room in rooms" :key="room.id">
                            <li v-if="canAccessRoom(room)" class="flex">
                                <Link
                                    :href="route('rooms.show', room.id)"
                                    class="p-2 hover:underline"
                                >
                                    {{ room.title }}
                                </Link>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
