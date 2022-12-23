// Ajouter un sous-formulaire d'ajout d'enfant
const addChild = (e) => {
    const collectionDiv = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement('div');
    item.classList.add('item-child');
    item.innerHTML = collectionDiv
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionDiv.dataset.index
        );
    item.querySelector('.btn-remove').addEventListener('click', () => item.remove());

    collectionDiv.appendChild(item);

    collectionDiv.dataset.index++;
}

document
    .querySelectorAll('.btn-remove')
    .forEach(btn => btn.addEventListener(
        'click',
        (e) => e.currentTarget.closest('.item-child').remove())
    );


document
    .querySelectorAll('.btn-new')
    .forEach(btn => btn.addEventListener('click', addChild));

// https://www.youtube.com/watch?v=OUKd0e2ph1Y&t=3456s 1h19
