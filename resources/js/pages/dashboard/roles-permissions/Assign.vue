<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Role, type Permission } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { ChevronDown } from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles Permissions Assign',
        href: '/roles-permissions-assign',
    },
];

const props = defineProps<{
    role: Role;
    permissions: Permission[];
    assignedPermissions: Array<{ id: number }>;
}>();

const form = useForm({
    permission_ids: props.assignedPermissions.map(permission => permission.id.toString()),
});

const groupedPermissions = computed(() => {
    return props.permissions.reduce((acc, permission) => {
        const group = permission.group_name || 'Other';
        if (!acc[group]) {
            acc[group] = [];
        }
        acc[group].push(permission);
        return acc;
    }, {} as Record<string, Permission[]>);
});

const submit = () => {
    form.put(route('roles.permissions.assign.store', props.role.id), {
        onSuccess: () => {
            toast.success('Permissions assigned successfully', {
                description: new Date().toLocaleString(),
            });
        },
    });
};
</script>

<template>
    <Head title="Role Permission Assign" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <Link :href="route('roles.create')" as="button" class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current dark:decoration-neutral-500">
                    Back to Roles
                </Link>
            </div>
            <div class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="flex flex-col gap-6 p-5">
                    <div class="grid gap-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
                            <div class="grid gap-2 w-full sm:w-auto flex-grow">
                                <span class="text-lg font-semibold">Assign Permissions for Role: <span class="font-normal">{{ props.role.name }}</span></span>
                            </div>
                            <!-- <div class="flex items-center gap-2 pt-2 sm:pt-0 self-start sm:self-center shrink-0">
                                <Checkbox id="selectAll"
                                />
                                <label for="selectAll" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Select All Permissions
                                </label>
                            </div> -->
                        </div>

                        <div class="grid gap-4">
                            <Label>Available Permissions</Label>
                            <InputError :message="form.errors.permission_ids" class="mt-1 mb-2" />
                            <div class="space-y-4">
                                <Collapsible
                                    v-for="(permissionsInGroup, groupName) in groupedPermissions"
                                    :key="groupName"
                                    class="rounded-lg border p-4"
                                    default-open
                                >
                                    <CollapsibleTrigger class="group flex w-full items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <!-- <Checkbox :id="`group-${groupName}`"/> -->
                                            <label :for="`group-${groupName}`" class="text-sm font-medium capitalize leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                {{ groupName }} <span class="text-xs text-muted-foreground">({{ permissionsInGroup.length }} permissions)</span>
                                            </label>
                                        </div>
                                        <ChevronDown class="h-4 w-4 transition-transform duration-200 group-data-[state=open]:rotate-180" />
                                    </CollapsibleTrigger>
                                    <CollapsibleContent class="mt-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-3 pl-6">
                                            <div v-for="permission in permissionsInGroup" :key="permission.id" class="flex items-center space-x-2">
                                                <Checkbox :id="`permission-${permission.id}`"
                                                    :model-value="permission.id.toString() === form.permission_ids.find(id => id === permission.id.toString())"
                                                    @update:model-value="(value) => {
                                                        if (value) {
                                                            form.permission_ids.push(permission.id.toString());
                                                        } else {
                                                            form.permission_ids = form.permission_ids.filter(id => id !== permission.id.toString());
                                                        }
                                                    }"
                                                />
                                                <label :for="`permission-${permission.id}`" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                    {{ permission.name }}
                                                </label>
                                            </div>
                                        </div>
                                    </CollapsibleContent>
                                </Collapsible>
                            </div>
                        </div>

                        <div class="flex justify-start">
                            <Button type="submit" class="mt-4 w-full sm:w-auto" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                                Update Permissions
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
