<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User, type Role } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, List } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Edit',
        href: '/users-edit',
    },
];

const props = defineProps<{
    user: User,
    roles: Role[],
    userRoles: Array<{ id: number }>,
}>();

const form = useForm({
    id: props.user.id,
    role_ids: props.userRoles.map(role => role.id.toString()),
    name: props.user.name,
    email: props.user.email,
});

const submit = () => {
    form.put(route('users.update', props.user.id), {
        onSuccess: () => {
            form.reset();
            toast.success('User updated successfully', {
                description: new Date().toLocaleString(),
            });
        },
    });
};

</script>

<template>
    <Head title="User Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <List />
                </Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label>Roles</Label>
                            <Select v-model="form.role_ids" multiple>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select roles">
                                        {{ form.role_ids.length > 0
                                            ? roles.filter(r => form.role_ids.includes(r.id.toString())).map(r => r.name).join(', ')
                                            : 'Select roles'
                                        }}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Roles</SelectLabel>
                                        <SelectItem
                                            v-for="role in roles"
                                            :key="role.id"
                                            :value="role.id.toString()"
                                        >
                                            {{ role.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.role_ids" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" v-model="form.name" placeholder="Name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="Email" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Update User
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
