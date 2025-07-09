<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SelectInput from '@/Components/SelectInput.vue';
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const submit = () => {
    form.errors = {};

    if (!form.name) {
        form.errors.name = 'Name is required.';
        return;
    }

    if (!form.email) {
        form.errors.email = 'Email is required.';
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) {
        form.errors.email = 'Email format is invalid.';
        return;
    }

    if (!form.password) {
        form.errors.password = 'Password is required.';
        return;
    }

    if (form.password !== form.password_confirmation) {
        form.errors.password_confirmation = 'Password confirmation does not match.';
        return;
    }

    if (!form.role) {
        form.errors.role = 'Role is required.';
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Enter your name"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    placeholder="Enter your email address"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="role" value="Role" />

                <SelectInput
                    v-model="form.role"
                    id="role"
                    name="role"
                    class="mt-1 block w-full">

                    <option disabled value="">-- Select a Role --</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="employee">Employee</option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <div class="relative">
                    <TextInput
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        placeholder="Enter your password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                        @click="showPassword = !showPassword">
                        <svg v-if="showPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M10 3c-4.546 0-7.923 4.158-7.997 4.253a.75.75 0 000 .494C2.077 8.842 5.454 13 10 13s7.923-4.158 7.997-4.253a.75.75 0 000-.494C17.923 7.158 14.546 3 10 3zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd"/>
                        </svg>
                        <svg v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M10 3C5.454 3 2.077 7.158 2.003 7.253a.75.75 0 000 .494C2.077 8.842 5.454 13 10 13s7.923-4.158 7.997-4.253a.75.75 0 000-.494C17.923 7.158 14.546 3 10 3z"/>
                            <path d="M10 11a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <div class="relative">
                    <TextInput 
                        :type="showConfirmPassword ? 'text' : 'password'"
                        id="password_confirmation"
                        placeholder="Confirm your password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"/>

                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                        @click="showConfirmPassword = !showConfirmPassword">
                        <svg v-if="showConfirmPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3c-4.546 0-7.923 4.158-7.997 4.253a.75.75 0 000 .494C2.077 8.842 5.454 13 10 13s7.923-4.158 7.997-4.253a.75.75 0 000-.494C17.923 7.158 14.546 3 10 3zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd"/>
                        </svg>
                        <svg v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M10 3C5.454 3 2.077 7.158 2.003 7.253a.75.75 0 000 .494C2.077 8.842 5.454 13 10 13s7.923-4.158 7.997-4.253a.75.75 0 000-.494C17.923 7.158 14.546 3 10 3z"/>
                            <path d="M10 11a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
