const authGuard = (routeName, authUser) => {
    if (routeName === "login" && authUser) {
        return { name: 'home' }
    }

    if (routeName === "register" && authUser) {
        return { name: 'home' }
    }

    if (routeName === "password.forgot" && authUser) {
        return {name: 'home'}
    }

    if (routeName === "password.reset" && authUser) {
        return {name: 'home'}
    }

    if (routeName === "email.verification" && !authUser) {
        return {name: 'login'}
    }

    if (routeName === "email.verification" && authUser && authUser.email_verified_at) {
        return {name: 'home'}
    }

    if (routeName === "home" && authUser && !authUser.email_verified_at) {
        return {name: 'email.verification'}
    }

    return null;
}

export default authGuard;