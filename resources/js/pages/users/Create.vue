<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Users', href: '/users' },
    { title: 'Create User', href: '/users' },
];

const props = defineProps({
    roles: { type: Array, required: true },
});

const form = useForm({
    roles: '',
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('users.store'), {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
    });
};

const isProcessing = computed(() => form.processing);
</script>

<template>
    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-end">
                <Link href="/users" class="btn btn-indigo-500 dark:bg-indigo-600 px-2 py-1 rounded">User List</Link>
            </div>
            <div class="min-w-96 mx-auto p-5 mt-8 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="role">Role</Label>
                            <Select :modelValue="form.roles" @update:modelValue="(value) => form.roles = value" id="role" placeholder="Select a role">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="role.name" v-for="role in roles" :key="role.id">
                                        {{ role.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.roles" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" autofocus autocomplete="name" v-model="form.name" placeholder="Full name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" type="email" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" autocomplete="new-password" v-model="form.password" placeholder="Password" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="isProcessing">
                            <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                            Create account
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
