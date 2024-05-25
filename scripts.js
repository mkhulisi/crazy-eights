document.addEventListener('DOMContentLoaded', function() {
    const ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    const suits = ['♣', '♦', '♥', '♠'];

    const deckContainer = document.querySelector('.deck');

    // Create cards for each rank and suit combination
    ranks.forEach(rank => {
        suits.forEach(suit => {
            const cardElement = document.createElement('div');
            cardElement.classList.add('card');

            const top = document.createElement('div');
            top.classList.add('top');
            top.innerHTML = `<div class="rank">${rank}</div><div class="suit">${suit}</div>`;
            cardElement.appendChild(top);

            const center = document.createElement('div');
            center.classList.add('center');
            center.innerHTML = suit;
            cardElement.appendChild(center);

            const bottom = document.createElement('div');
            bottom.classList.add('bottom');
            bottom.innerHTML = `<div class="rank">${rank}</div><div class="suit">${suit}</div>`;
            cardElement.appendChild(bottom);

            deckContainer.appendChild(cardElement);
        });
    });
});
