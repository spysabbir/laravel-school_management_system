<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, onMounted, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const { currentUser } = defineProps<{
    currentUser: {
        id: number;
        avatar?: string | null;
        name: string;
        email: string;
    };
}>();

const form = useForm({
    avatar: null as File | null,
    name: currentUser.name,
    email: currentUser.email,
    _method: 'PATCH',
})

const currentAvatarPreview = ref<string | null>(currentUser.avatar ? '/uploads/avatars/' + currentUser.avatar : null);
const avatarPreview = ref<string | null>(null);

const submit = () => {
    form.post(route('profile.update'), {
        onFinish: () => {
            form.reset('avatar');
        },
    });
};

const onAvatarChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    } else {
        form.avatar = null;
        avatarPreview.value = null;
    }
};

const isProcessing = computed(() => form.processing);

onMounted(() => {
    if (currentUser.avatar) {
        currentAvatarPreview.value = '/uploads/avatars/' + currentUser.avatar;
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid gap-2">
                        <Label for="avatar">Avatar</Label>
                        <Input id="avatar" class="mt-1 block w-full" type="file" @change="onAvatarChange" accept="image/*" />
                        <div class="flex space-x-2">
                            <img v-if="currentAvatarPreview" :src="currentAvatarPreview" :class="{ 'opacity-50': form.avatar }" class="w-20 h-20 rounded-full" alt="Current Avatar" />
                            <img v-if="avatarPreview" :src="avatarPreview" class="w-20 h-20 rounded-full" alt="New Avatar Preview" />
                        </div>
                        <InputError :message="form.errors.avatar" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="username" placeholder="Email address" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" class="mt-2 w-full" tabindex="4" :disabled="isProcessing">
                            <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                            Update account
                        </Button>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
