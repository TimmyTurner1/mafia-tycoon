// Example JavaScript to update user stats dynamically
document.addEventListener("DOMContentLoaded", () => {
    // This would come from the backend or user data
    const userName = 'John Doe';
    const userMoney = 5000;
    const userHealth = 100;

    document.getElementById('userName').innerText = userName;
    document.getElementById('userMoney').innerText = userMoney;
    document.getElementById('userHealth').innerText = userHealth;
});
