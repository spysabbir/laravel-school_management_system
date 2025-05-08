<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Role } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { FolderSync, Plus, CopyX, Trash } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles And Permissions',
        href: '/roles-permissions',
    },
];

defineProps<{
    roles: Role[],
}>();

const confirmDelete = (roleId: number) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('roles.destroy', roleId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
                toast.success('Role deleted successfully', {
                    description: new Date().toLocaleString(),
                });
            },
        });
    }
};

const confirmRevoke = (roleId: number) => {
    if (confirm('Are you sure you want to revoke this role?')) {
        router.get(route('roles.permissions.revoke', roleId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Roles And Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('roles.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <Plus class="h-5 w-5" />
                </Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table>
                    <TableCaption>A list of your roles</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Sl No</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(role, index) in roles" :key="index">
                            <TableCell>{{ index+1 }}</TableCell>
                            <TableCell>{{ role.name }}</TableCell>
                            <TableCell class="flex items-center gap-2">
                                <Link :href="route('roles.permissions.assign', role.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200"><FolderSync class="h-5 w-5" /></Link>
                                <button @click="confirmRevoke(role.id)" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200"><CopyX class="h-5 w-5"/></button>
                                <button @click="confirmDelete(role.id)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200"><Trash class="h-5 w-5" /></button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
