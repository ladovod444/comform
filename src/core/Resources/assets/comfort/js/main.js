function executeFunc(func)
{

    if(typeof func !== 'function')
    {
        return false;
    }

    func();
    return true;
}

function changeButton()
{
    const offcanvases = document.querySelectorAll('[id^="Menu"]');
    const cross = document.querySelector('.bi-x-lg');
    const list = document.querySelector('.bi-list');

    let isAnyOffcanvasOpen = false;

    offcanvases.forEach((offcanvas, index) =>
    {
        offcanvas.addEventListener('shown.bs.offcanvas', () =>
        {
            isAnyOffcanvasOpen = true;
            try
            {
                cross.style.display = 'block';
                list.style.display = 'none';
            } catch(e)
            {}
        });

        offcanvas.addEventListener('hidden.bs.offcanvas', () =>
        {
            const isOpen = Array.from(offcanvases).some(offcanvas => offcanvas.classList.contains('show'));
            if(!isOpen)
            {
                isAnyOffcanvasOpen = false;
                try
                {
                    cross.style.display = 'none';
                    list.style.display = 'block';
                } catch(e)
                {}
            }
        });
    });
}

function dropdownCatalog()
{
    const btn = document.getElementById('dropdownMainMenuBtn');
    const menu = document.getElementById('dropdownMainMenu');


    btn.addEventListener('click', () =>
    {
        menu.classList.toggle('show')
    });

    document.addEventListener('click', (event) =>
    {
        if(!btn.contains(event.target) && !menu.contains(event.target))
        {
            menu.classList.remove('show');
        }
    });


}

executeFunc(changeButton);
executeFunc(dropdownCatalog);



