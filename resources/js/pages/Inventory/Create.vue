<script setup lang="ts">
import InventoryController from '@/actions/App/Http/Controllers/InventoryController';
import { index } from '@/routes/inventory';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];
</script>

<template>
    <Head title="Create Inventory Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <HeadingSmall
                title="Create Inventory Item"
                description="Add a new item to your inventory"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Item Details</CardTitle>
                    <CardDescription>Enter the details for the new inventory item</CardDescription>
                </CardHeader>
                <CardContent>
                    <Form
                        v-bind="InventoryController.store.form()"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Name *</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    type="text"
                                    required
                                    placeholder="Item name"
                                    aria-invalid="errors.name ? 'true' : 'false'"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="sku">SKU *</Label>
                                <Input
                                    id="sku"
                                    name="sku"
                                    type="text"
                                    required
                                    placeholder="SKU-1234-ABC"
                                    aria-invalid="errors.sku ? 'true' : 'false'"
                                />
                                <InputError :message="errors.sku" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                    placeholder="Item description..."
                                    aria-invalid="errors.description ? 'true' : 'false'"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="category">Category</Label>
                                <Input
                                    id="category"
                                    name="category"
                                    type="text"
                                    placeholder="e.g., Electronics, Clothing"
                                    aria-invalid="errors.category ? 'true' : 'false'"
                                />
                                <InputError :message="errors.category" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="location">Location</Label>
                                <Input
                                    id="location"
                                    name="location"
                                    type="text"
                                    placeholder="e.g., Warehouse A, Store Front"
                                    aria-invalid="errors.location ? 'true' : 'false'"
                                />
                                <InputError :message="errors.location" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="quantity">Quantity *</Label>
                                <Input
                                    id="quantity"
                                    name="quantity"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                    aria-invalid="errors.quantity ? 'true' : 'false'"
                                />
                                <InputError :message="errors.quantity" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="price">Price *</Label>
                                <Input
                                    id="price"
                                    name="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="0.00"
                                    aria-invalid="errors.price ? 'true' : 'false'"
                                />
                                <InputError :message="errors.price" />
                            </div>

                            <div class="flex items-center gap-2 md:col-span-2">
                                <input type="hidden" name="is_active" value="0" />
                                <Checkbox
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    :default-checked="true"
                                />
                                <Label for="is_active" class="cursor-pointer">
                                    Active (Item is available)
                                </Label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Creating...' : 'Create Item' }}
                            </Button>
                            <Link :href="index().url">
                                <Button variant="outline">Cancel</Button>
                            </Link>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-green-600"
                                >
                                    Item created successfully.
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

