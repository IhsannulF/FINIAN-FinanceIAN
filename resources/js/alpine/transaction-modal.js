export default function transactionModal(storeUrl) {
    return {
        open: false,
        mode: 'create',
        storeUrl: storeUrl,
        formAction: storeUrl,
        form: {
            type: 'expense',
            category_id: '',
            amount: '',
            transaction_date: new Date().toISOString().slice(0, 10),
            description: '',
        },

        openCreate() {
            this.mode = 'create';
            this.formAction = this.storeUrl;
            this.form = {
                type: 'expense',
                category_id: '',
                amount: '',
                transaction_date: new Date().toISOString().slice(0, 10),
                description: '',
            };
            this.open = true;
        },

        openEdit(transaction) {
            this.mode = 'edit';
            this.formAction = '/transactions/' + transaction.id;
            this.form = {
                type: transaction.type,
                category_id: transaction.category_id,
                amount: transaction.amount,
                transaction_date: transaction.transaction_date,
                description: transaction.description ?? '',
            };
            this.open = true;
        },

        close() {
            this.open = false;
        },

        selectedCategoryIcon() {
            const el = document.querySelector('#category_id option[value="' + this.form.category_id + '"]');
            return el ? el.dataset.icon : 'ph ph-sparkle';
        },

        selectedCategoryColor() {
            const el = document.querySelector('#category_id option[value="' + this.form.category_id + '"]');
            return el ? el.dataset.color : '#7132f5';
        },
    };
}