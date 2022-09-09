<div id="notifications">
</div>

<script>
  window.notifyJs = (content, type = 'success') => {
    $notify = document.createElement('div');
    $notify.classList.add('notify');
    $notify.classList.add(`notify-${type}`);

    $notifyContent = document.createElement('div');
    $notifyContent.classList.add('notify-content');
    $notifyContent.innerHTML = content;
    $notify.appendChild($notifyContent);

    document.getElementById('notifications').appendChild($notify);
  }
  window.responseErrorHandler = (e) => {
    const title = e.message;
    if (e.response.status === 422) {
      const message = `<p>${title}</p><ul><li>${e.response.data.message}</li></ul>`;
      notifyJs(message, 'danger');
      return;
    }
    notifyJs(title, 'danger');
  }

  document.addEventListener('DOMContentLoaded', function() {
    watchForNotifications(document.getElementById('notifications'));
    const DELAY_OUT = 5000;
    const alerts = [];
    const errors = @json($errors->all('<li class="list-disc">:message</li>'));
    const notifications = @json($notifications);

    if (errors.length > 0) {
      alerts.push({
        content: `<ul class="ml-4">${errors.join('')}</ul>`,
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
      window.notifyJs(alert.content, alert.type);
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
</script>
