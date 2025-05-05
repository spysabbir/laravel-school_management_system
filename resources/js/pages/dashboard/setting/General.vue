<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'General Setting',
        href: '/setting/general',
    },
];

const props = defineProps<{
    generalSetting: {
        app_name?: string;
        app_url?: string;
        app_email?: string;
        app_phone?: string;
        app_address?: string;
        app_logo?: string;
        app_favicon?: string;
        app_timezone?: string;
    };
}>();

const form = useForm({
    app_name: props.generalSetting?.app_name || '',
    app_url: props.generalSetting?.app_url || '',
    app_email: props.generalSetting?.app_email || '',
    app_phone: props.generalSetting?.app_phone || '',
    app_address: props.generalSetting?.app_address || '',
    app_logo: null as File | null,
    app_favicon: null as File | null,
    app_timezone: props.generalSetting?.app_timezone || 'UTC',
});

const handleFile = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        form.app_logo = fileInput.files[0];
    }
};

const handleFaviconFile = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        form.app_favicon = fileInput.files[0];
    }
};

const submit = () => {
    form.post(route('general.setting.update'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="General Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
    {{ props.generalSetting.app_name }}

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <h1 class="text-2xl font-bold">General Settings</h1>
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="app_name">App Name</Label>
                            <Input id="app_name" type="text" v-model="form.app_name" placeholder="App Name" />
                            <InputError :message="form.errors.app_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="app_url">App URL</Label>
                            <Input id="app_url" type="text" v-model="form.app_url" placeholder="App URL" />
                            <InputError :message="form.errors.app_url" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_email">App Email</Label>
                            <Input id="app_email" type="email" v-model="form.app_email" placeholder="App Email" />
                            <InputError :message="form.errors.app_email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_phone">App Phone</Label>
                            <Input id="app_phone" type="text" v-model="form.app_phone" placeholder="App Phone" />
                            <InputError :message="form.errors.app_phone" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_address">App Address</Label>
                            <Input id="app_address" type="text" v-model="form.app_address" placeholder="App Address" />
                            <InputError :message="form.errors.app_address" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_logo">App Logo</Label>
                            <Input id="app_logo" type="file" @change="handleFile" />
                            <InputError :message="form.errors.app_logo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_favicon">App Favicon</Label>
                            <Input id="app_favicon" type="file" @change="handleFaviconFile" />
                            <InputError :message="form.errors.app_favicon" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="app_timezone">App Timezone</Label>
                            <select id="app_timezone" v-model="form.app_timezone" class="rounded-md border border-gray-300 p-2">
                                <option value="UTC">UTC</option>
                                <!-- Add more timezone options as needed -->
                            </select>
                            <InputError :message="form.errors.app_timezone" />
                        </div>
                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Update Settings
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
