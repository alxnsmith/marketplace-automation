<div id="notifications">
</div>

<script>
  (() => {
    class TAG {
      static p = (html) => `<p>${html}</p>`
      static ul = (html) => `<ul class="ml-4">${html}</ul>`
      static li = (html) => `<li class="list-disc">${html}</li>`
    }

    class Notification {
      constructor(content, type) {
        console
        this.content = content;
        this.type = type;
      }

      get $el() {
        const $tmpEl = document.createElement('div');
        $tmpEl.innerHTML = /*html*/ `
          <div class="notify notify-${this.type}">
            <div class="notify-content">
              ${this.content}
            </div>
          </div>
        `;
        return $tmpEl.firstElementChild;
      }

      static trigger(content, type = 'successs') {
        const notification = new Notification(content, type);
        const $el = notification.$el;
        document.getElementById('notifications').appendChild($el);
        return notification;
      }
    }

    window.notifyJs = Notification.trigger;

    window.responseErrorHandler = (e) => {
      const title = e.message;
      if (e.response.status === 422) {
        const message = TAG.p(title) + TAG.ul(TAG.li(e.response.data.message));
        notifyJs(message, 'danger');
        return;
      }
      notifyJs(title, 'danger');
    }

    document.addEventListener('DOMContentLoaded', function() {
      watchForNotifications(document.getElementById('notifications'));
      const DELAY_OUT = 5000;
      const alerts = [];
      const errors = @json($errors->all()).map(e => TAG.li(e));
      const notifications = @json($notifications);

      if (errors.length > 0) {
        alerts.push({
          content: TAG.ul(errors.join('')),
          type: 'danger'
        });
      }
      if (notifications.length > 0) {
        notifications.forEach(notification => {
          alerts.push({
            content: notification.content,
            type: notification.type
          });
        });
      }


      alerts.forEach(alert => {
        Notification.trigger(alert.content, alert.type);
      });


      // Observe for $notifications child nodes, and show them
      function watchForNotifications($node) {
        new MutationObserver((mutations) => {
          mutations.forEach((mutation) => {
            mutation.addedNodes.forEach(($notify) => {
              setTimeout(() => $notify.remove(), DELAY_OUT);
            });
          });
        }).observe($node, {
          childList: true
        });
      }
    });
  })()
</script>
