<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const { room } = defineProps({
    room: Object,
    participants: Array,
});

// +++> Message input
const form = useForm({
    message: "",
});

const inputMessage = ref(null);
const focusInputMessage = () => {
    setTimeout(() => {
        inputMessage.value.focus();
    }, 0);
};

const send = async () => {
    form.post(`/rooms/${room.id}/messages`, {
        onError: (error) => console.log(error),
        onSuccess: () => {
            form.reset("message");
            focusInputMessage();
        },
    });
};

// +++> Message list
const sortedParticipants = computed(() => {
    return (
        room.participants
            // sort alphabetically by name
            .sort((a, b) => a.name.localeCompare(b.name))

            // sort active
            .sort((participant) => {
                return activeParticipants.value.includes(participant.id)
                    ? -1
                    : 1;
            })
    );
});

const activeParticipants = ref([]);
const inActiveParticipants = (participantId) => {
    return activeParticipants.value.includes(participantId);
};

// WS
const publicChannel = computed(() => window.Echo.channel("messages"));
// const privateChannel = computed(() =>
//     window.Echo.private("messages." + room.id)
// );
const presenceChannel = computed(() => window.Echo.join("messages." + room.id));

publicChannel.value.listen("MessageCreated", (e) => {
    room.messages.push(e.message);
});

//! BRB

// privateChannel.value.listen("MessageCreated", (e) => {
//     room.messages.push(e.message);
// });

presenceChannel.value
    .here((users) => {
        activeParticipants.value = users.map(({ id }) => id);
    })
    .joining((user) => {
        activeParticipants.value.push(user.id);
    })
    .leaving((user) => {
        activeParticipants.value = activeParticipants.value.filter(
            (id) => id !== user.id
        );
    })
    .error((error) => {
        Swal.fire({
            text: "Something went wrong.",
            icon: "error",
        });
    })
    .listen("MessageCreated", (e) => {
        console.log("created", e);
        room.messages.push(e.message);
    });

onMounted(() => {
    focusInputMessage();
});
</script>

<template>
    <AppLayout title="All Rooms">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Room - {{ room.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="flex min-h-[60vh]">
                        <section class="flex flex-col flex-1 p-4">
                            <div class="flex-1 pb-4">
                                <ul
                                    class="flex flex-col max-h-[50vh] overflow-y-auto custom-scrollbar"
                                >
                                    <li
                                        v-for="message in room.messages"
                                        :key="message.id"
                                        class="p-2 rounded-md even:bg-gray-100"
                                    >
                                        {{ message.body }}
                                    </li>
                                </ul>
                            </div>

                            <!-- input-message -->
                            <div>
                                <input
                                    ref="inputMessage"
                                    v-model="form.message"
                                    :disabled="form.processing"
                                    @keypress.enter="send"
                                    type="text"
                                    placeholder="message..."
                                    class="block w-full text-gray-700 border-gray-300 rounded-md"
                                />
                                <span
                                    class="ml-2 text-sm text-red-400"
                                    v-if="form.errors.message"
                                >
                                    {{ form.errors.message }}
                                </span>
                            </div>
                        </section>
                        <section class="w-[15rem] p-4">
                            <ul class="flex flex-col">
                                <li
                                    v-for="participant in sortedParticipants"
                                    :key="participant.id"
                                    :class="[
                                        'p-2 text-sm',
                                        !inActiveParticipants(participant.id) &&
                                            'text-gray-400',
                                    ]"
                                >
                                    {{ participant.email }}
                                </li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
