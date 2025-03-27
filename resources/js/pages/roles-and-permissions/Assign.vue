<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

import { toast } from 'vue-sonner'

import { ref, computed } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles And Permissions',
        href: '/roles-and-permissions',
    },
];

const isProcessing = computed(() => form.processing);

const props = defineProps({
  role: { type: Object, required: true, },
  permissions: { type: Array, required: true, },
});

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('roles-and-permissions.assignPermission'), {
        onSuccess: () => {
            toast.success('Permission assigned successfully');
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Roles And Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

        </div>
    </AppLayout>
</template>
