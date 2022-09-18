document.addEventListener('alpine:init', () => {
  const Status = {
    CANCELLED: "Заказ отменен.",
    DELIVERED: "Заказ получен покупателем.",
    DELIVERY: "Заказ передан в доставку.",
    PICKUP: "Заказ доставлен в пункт самовывоза.",
    PROCESSING: "Заказ находится в обработке.",
    REJECTED: "Заказ создан, но не оплачен.",
    UNPAID: "Заказ оформлен, но еще не оплачен (если выбрана оплата при оформлении).",
  }

  const SubStatus = {
    STARTED: "Заказ подтвержден, его можно начать обрабатывать.",
    READY_TO_SHIP: "Заказ собран и готов к отправке.",
    SHIPPED: "Заказ передан службе доставки.",
    CUSTOM: "Причина отмены заказа в свободной форме.",
    FULL_NOT_RANSOM: "Покупатель отказался покупать все товары из заказа.",
    PROCESSING_EXPIRED: "Магазин не обработал заказ в течение семи дней.",
    REPLACING_ORDER: "Покупатель решил заменить товар другим по собственной инициативе.",
    RESERVATION_EXPIRED: "Покупатель не завершил оформление зарезервированного заказа в течение 10 минут.",
    SHOP_FAILED: "Магазин не может выполнить заказ.",
    USER_BOUGHT_CHEAPER: "Покупатель нашел дешевле.",
    USER_CHANGED_MIND: "Покупатель отменил заказ по личным причинам.",
    USER_NOT_PAID: "Покупатель не оплатил заказ (для типа оплаты PREPAID) в течение 30 минут.",
    USER_REFUSED_DELIVERY: "Покупателя не устроили условия доставки.",
    USER_REFUSED_PRODUCT: "Покупателю не подошел товар.",
    USER_REFUSED_QUALITY: "Покупателя не устроило качество товара.",
    USER_UNREACHABLE: "Не удалось связаться с покупателем.",
    USER_WANTS_TO_CHANGE_ADDRESS: "Покупатель хочет изменить адрес доставки.",
    USER_WANTS_TO_CHANGE_DELIVERY_DATE: "Покупатель хочет изменить дату доставки.",
  }
  //  make status and substatus objects readonly props
  Object.keys(Status).forEach(key => Object.defineProperty(Status, key, { writable: false }))
  Object.keys(SubStatus).forEach(key => Object.defineProperty(SubStatus, key, { writable: false }))

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

    _status(status) {
      return Status[status];
    },
    _substatus(substatus) {
      return SubStatus[substatus];
    },
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