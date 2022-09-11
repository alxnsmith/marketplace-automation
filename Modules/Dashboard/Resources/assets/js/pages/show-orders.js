document.addEventListener('alpine:init', () => {
  Alpine.data('orders', () => ({
    form: {
      actions: ['get_labels', 'ready_to_ship'],
      orders: {},
    },

    doing_actions: [],
    orders: window.Orders,
    status: 'Form',
    status_label: 'Обработка заказов',
    message: 'Загрузка',

    init() {
      if (this.orders.length == 0) this.status = 'Empty';
    },

    get $checkAll() { return document.querySelector('[data-action="check_all"]') },
    get $checkboxes() { return document.querySelectorAll('[data-action="check_order"]') },

    checkAll() {
      const checked = this.$checkAll.checked;
      this.$checkboxes.forEach($el => $el.checked = checked);

      if (checked) Object.assign(this.form.orders, this.orders);
      else this.form.orders = {};
    },
    checkOrder(event, order) {
      this.$checkAll.checked = [...this.$checkboxes].every($el => $el.checked);

      const checked = event.target.checked;
      if (checked) this.form.orders[order.id] = order;
      else delete this.form.orders[order.id];
    },

    async action(e) {
      const $form = e.target;
      const url = $form.getAttribute('action');
      const method = $form.method;

      const response = await axios({
        method, url, data: this.form
      }).catch(responseErrorHandler);

      this.status = response.data.status;
      this.channel = response.data.channel;

      this.doing_actions = Object.fromEntries(this.form.actions.map(action => ([
        action,
        { name: action, ...response.data.actions[action] }
      ])));
    }
  }));

  Alpine.data('actionsProgress', (key) => ({
    status: 'Processing',
    message: 'В очереди',

    init() {
      this.action = this.doing_actions[key];

      Echo.private(this.channel).listenToAll(this.handlers[key].bind(this));
    },

    handlers: {
      get_labels(e, data) {
        if (e !== '.get_labels') return;
        this.status = data.status;
        if (data.status == 'Processing' && data.type == 'Text') this.message = data.message;
        else if (data.status == 'Success' && data.type == 'Array') {
          this.message = `<a class="btn btn-success" target="blank" href="${data.message.url}">Скачать этикетки</a>`;
        }
      },
      ready_to_ship(e, data) {
        if (e !== '.ready_to_ship') return;
        this.status = data.status;
        if (data.type == 'Text') this.message = data.message;
      }
    }
  }));
})