<script setup lang="ts">
import InventoryController from '@/actions/App/Http/Controllers/InventoryController';
import { create, edit, destroy } from '@/routes/inventory';
import { index } from '@/routes/inventory';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Form } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';

interface Inventory {
    id: number;
    name: string;
    description?: string;
    sku: string;
    quantity: number;
    price: number;
    category?: string;
    location?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    inventories: {
        data: Inventory[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
        category?: string;
    };
    categories: string[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: index().url,
    },
];

const search = ref(props.filters.search || '');
const categoryFilter = ref(props.filters.category || '');

const deleteInventoryId = ref<number | null>(null);

const isDeleteDialogOpen = (itemId: number): boolean => {
    return deleteInventoryId.value === itemId;
};

const openDeleteDialog = (itemId: number): void => {
    deleteInventoryId.value = itemId;
};

const closeDeleteDialog = (): void => {
    deleteInventoryId.value = null;
};

const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const handleSearch = (): void => {
    router.get(
        index().url,
        {
            search: search.value || undefined,
            category: categoryFilter.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const handleDelete = (inventory: Inventory): void => {
    router.delete(destroy({ inventory: inventory.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteDialog();
        },
    });
};

const clearFilters = (): void => {
    search.value = '';
    categoryFilter.value = '';
    router.get(index().url, {}, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Inventory Management"
                    description="Manage your inventory items"
                />
                <Link :href="create().url">
                    <Button>Add New Item</Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Filters</CardTitle>
                    <CardDescription>Search and filter inventory items</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
                        <div class="flex-1">
                            <Label for="search">Search</Label>
                            <Input
                                id="search"
                                v-model="search"
                                type="text"
                                placeholder="Search by name, SKU, category, or description..."
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="flex-1">
                            <Label for="category">Category</Label>
                            <select
                                id="category"
                                v-model="categoryFilter"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                                @change="handleSearch"
                            >
                                <option value="">All Categories</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat"
                                    :value="cat"
                                >
                                    {{ cat }}
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <Button @click="handleSearch">Search</Button>
                            <Button variant="outline" @click="clearFilters">
                                Clear
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Inventory Items</CardTitle>
                    <CardDescription>
                        Total: {{ inventories.total }} items
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="inventories.data.length === 0"
                        class="py-12 text-center"
                    >
                        <p class="text-muted-foreground">No inventory items found.</p>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Name
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        SKU
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Category
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Quantity
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Price
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Location
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-right text-sm font-medium">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in inventories.data"
                                    :key="item.id"
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ item.name }}</div>
                                        <div
                                            v-if="item.description"
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{ item.description.substring(0, 50) }}
                                            {{ item.description.length > 50 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <code class="rounded bg-muted px-2 py-1 text-xs">
                                            {{ item.sku }}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge v-if="item.category" variant="outline">
                                            {{ item.category }}
                                        </Badge>
                                        <span v-else class="text-muted-foreground">—</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            :variant="
                                                item.quantity === 0
                                                    ? 'destructive'
                                                    : item.quantity < 10
                                                      ? 'secondary'
                                                      : 'default'
                                            "
                                        >
                                            {{ item.quantity }}
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ item.location || '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            :variant="item.is_active ? 'default' : 'secondary'"
                                        >
                                            {{ item.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="edit({ inventory: item.id }).url"
                                            >
                                                <Button variant="outline" size="sm">
                                                    Edit
                                                </Button>
                                            </Link>
                                            <Dialog
                                                :open="isDeleteDialogOpen(item.id)"
                                                @update:open="
                                                    (open) => {
                                                        if (!open) {
                                                            closeDeleteDialog();
                                                        }
                                                    }
                                                "
                                            >
                                                <DialogTrigger as-child>
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                        @click="openDeleteDialog(item.id)"
                                                    >
                                                        Delete
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent>
                                                    <DialogHeader>
                                                        <DialogTitle>
                                                            Delete Inventory Item
                                                        </DialogTitle>
                                                        <DialogDescription>
                                                            Are you sure you want to delete
                                                            "{{ item.name }}"? This action
                                                            cannot be undone.
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <DialogFooter>
                                                        <DialogClose as-child>
                                                            <Button
                                                                variant="outline"
                                                                @click="closeDeleteDialog"
                                                            >
                                                                Cancel
                                                            </Button>
                                                        </DialogClose>
                                                        <Button
                                                            variant="destructive"
                                                            @click="handleDelete(item)"
                                                        >
                                                            Delete
                                                        </Button>
                                                    </DialogFooter>
                                                </DialogContent>
                                            </Dialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="inventories.last_page > 1"
                        class="mt-4 flex items-center justify-between"
                    >
                        <div class="text-sm text-muted-foreground">
                            Showing {{ inventories.data.length }} of
                            {{ inventories.total }} items
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in inventories.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm rounded-md border transition-colors',
                                    link.active
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'hover:bg-muted',
                                    !link.url
                                        ? 'opacity-50 cursor-not-allowed pointer-events-none'
                                        : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

