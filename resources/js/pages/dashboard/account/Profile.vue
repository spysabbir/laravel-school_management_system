<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import AccountLayout from '@/layouts/account/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ref, onUnmounted } from 'vue';
import { Textarea } from '@/components/ui/textarea'
import { toast } from 'vue-sonner'

import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'

type BloodGroup = 'A+' | 'A-' | 'B+' | 'B-' | 'AB+' | 'AB-' | 'O+' | 'O-' | null;

interface ProfileForm {
    profile_photo: File | null;
    name: string;
    email: string;
    blood_group: BloodGroup;
    permanent_address: string | null;
    _method: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const existingProfilePhotoUrl = ref<string | null>(
    user.profile_photo ? `/uploads/profile_photos/${user.profile_photo}` : null
);
const newProfilePhotoFile = ref<File | null>(null);
const newProfilePhotoPreviewUrl = ref<string | null>(null);

const clearPhotoPreview = () => {
    if (newProfilePhotoPreviewUrl.value) {
        URL.revokeObjectURL(newProfilePhotoPreviewUrl.value);
        newProfilePhotoPreviewUrl.value = null;
    }
};

const onProfilePhotoChange = (e: Event) => {
    clearPhotoPreview();
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    newProfilePhotoFile.value = file;
    if (file) {
        newProfilePhotoPreviewUrl.value = URL.createObjectURL(file);
    }
};

const form = useForm<ProfileForm>({
    profile_photo: null,
    name: user.name,
    email: user.email,
    blood_group: user.blood_group as BloodGroup,
    permanent_address: user.permanent_address,
    _method: 'PATCH',
});

onUnmounted(() => {
    clearPhotoPreview();
});

const submit = () => {
    form.profile_photo = newProfilePhotoFile.value;

    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            const fileInput = document.getElementById('profile_photo') as HTMLInputElement;
            if (fileInput) fileInput.value = '';

            toast.success('Profile updated successfully', {
                description: new Date().toLocaleString(),
            });

            if (newProfilePhotoPreviewUrl.value) {
                existingProfilePhotoUrl.value = newProfilePhotoPreviewUrl.value;
                newProfilePhotoPreviewUrl.value = null;
                newProfilePhotoFile.value = null;
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <AccountLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address and other details" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="profile_photo">Profile Photo</Label>
                        <Input id="profile_photo" type="file" class="mt-1 block w-full" @change="onProfilePhotoChange" accept="image/*" />
                        <div class="flex space-x-2">
                            <img v-if="existingProfilePhotoUrl" :src="existingProfilePhotoUrl" class="w-20 h-20 rounded-full object-cover" alt="Current Profile Photo" />
                            <div v-else class="w-20 h-20 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <img v-if="newProfilePhotoPreviewUrl" :src="newProfilePhotoPreviewUrl" class="w-20 h-20 rounded-full object-cover" alt="New Profile Photo Preview" />
                        </div>
                        <InputError class="mt-2" :message="form.errors.profile_photo" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" placeholder="Email address" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="blood_group">Blood Group</Label>
                        <Select v-model="form.blood_group"> <SelectTrigger>
                                <SelectValue placeholder="Select a blood group" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Blood Group</SelectLabel>
                                    <SelectItem value="A+">A+</SelectItem>
                                    <SelectItem value="A-">A-</SelectItem>
                                    <SelectItem value="B+">B+</SelectItem>
                                    <SelectItem value="B-">B-</SelectItem>
                                    <SelectItem value="AB+">AB+</SelectItem>
                                    <SelectItem value="AB-">AB-</SelectItem>
                                    <SelectItem value="O+">O+</SelectItem>
                                    <SelectItem value="O-">O-</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.blood_group" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="permanent_address">Permanent Address</Label>
                        <Textarea v-model="form.permanent_address" placeholder="Enter your permanent address" />
                        <InputError class="mt-2" :message="form.errors.permanent_address" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">Save</Button>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </AccountLayout>
    </AppLayout>
</template>
