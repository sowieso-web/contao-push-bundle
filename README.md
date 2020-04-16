# Contao Push Bundle

Send Push Notifications with Contao.

This bundle uses [the official PHP implementation](https://github.com/web-push-libs/web-push-php) for Web Push.

## Useful links
- [Generating VAPID keys](https://github.com/web-push-libs/web-push-php#authentication-vapid)
- [Symfony Configuration](https://github.com/minishlink/web-push-bundle#configuration)

## Usage
- Configure your VAPID keys. Use the link above to see how to generate them with `openssl`. The minimal configuration is:

```yaml
minishlink_web_push:
  VAPID:
    subject: https://yoursite.com
    publicKey: ~88 chars          
    privateKey: ~44 chars
```

- Create the front end module and insert it on a page. This is a button to subscribe to push notifications. Nobody wants to allow them on the initial page load :-)
- There will be a new Javascript file in your public directory. This is a file that registers a service worker so you can receive push notifications. It is added automatically with the module.
- Use the button to subscribe.
- In the back end there is a new button in the news list to trigger a notification. It should be the last icon in a row with the green "up" arrow. 

In the future we will add more features, e.g. a form to modify the content, custom icons or custom vibration support. Feel free to contribute!
