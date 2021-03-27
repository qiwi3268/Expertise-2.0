@extends('components.app')

@section('title', 'Навигация')

@prepend('resources')
   <script src="{{ mix('js/navigation.js') }}"></script>
   <link rel="stylesheet" type="text/css" href="{{ mix('css/navigation.css') }}">
@endprepend


@section('body')
   <x-header/>

   <div class="content">

      <div class="sub-header">
         <div class="sub-header__block">
            <div class="sub-header__label">Подать заявление</div>
            <div class="sub-header__circle green">
               <i class="sub-header__icon fas fa-pen-alt"></i>
            </div>
         </div>
      </div>

      <div class="navigation">

         <div class="navigation__sidebar view-sidebar">
            <div class="view-sidebar__block">
               <div class="view-sidebar__title">Мои документы</div>
               <div class="view-sidebar__body">
                  <div class="view-sidebar__row">
                     <div class="view-sidebar__label">Мои заявления</div>
                     <div class="view-sidebar__counter">10</div>
                  </div>
                  <div class="view-sidebar__row">
                     <div class="view-sidebar__label">Мои договоры</div>
                     <div class="view-sidebar__counter">3</div>
                  </div>
                  <div class="view-sidebar__row">
                     <div class="view-sidebar__label">Мои счета</div>
                     <div class="view-sidebar__counter">0</div>
                  </div>
               </div>
            </div>
            <div class="view-sidebar__block">
               <div class="view-sidebar__title">Заявления по сроку</div>
               <div class="view-sidebar__body">
                  <div class="view-sidebar__row">
                     <div class="view-sidebar__label">Просроченные</div>
                     <div class="view-sidebar__counter">0</div>
                  </div>
                  <div class="view-sidebar__row">
                     <div class="view-sidebar__label">Истекает срок</div>
                     <div class="view-sidebar__counter">1</div>
                  </div>
               </div>
            </div>
         </div>


         <div class="navigation__content">
            <div class="navigation__search search">
               <div class="search__body">
                  <i class="search__icon fas fa-search"></i>
                  <input class="search__input" type="text" placeholder="Поиск по документам"/>
               </div>
               <div class="search__button">Поиск</div>
            </div>
            <div class="navigation__body">
               <div class="navigation__table navigation-table">
                  <div class="navigation-table__nav">
                     <div class="navigation-table__pagination pagination">
                        <i class="pagination__arrow fas fa-chevron-left"></i>
                        <div class="pagination__label">1 из 5</div>
                        <i class="pagination__arrow fas fa-chevron-right"></i>
                     </div>
                     <div class="navigation-table__size">
                        <div class="navigation-table__amount">25</div>
                        <div class="navigation-table__amount">50</div>
                        <div class="navigation-table__amount">75</div>
                     </div>
                     <div class="navigation-table__sort">
                        <div class="navigation-table__category">
                           <div class="navigation-table__category-label">Id заявления</div>
                           <i class="navigation-table__sort-icon fas fa-caret-up"></i>
                        </div>
                     </div>
                  </div>
                  <table>
                     <tbody>
                     <tr>
                        <th>Россия</th>
                        <th>Великобритания</th>
                        <th>Европа</th>
                        <th>Длина ступни, см</th>
                     </tr>
                     <tr>
                        <td>
                           <div class="test-flex">
                              <div class="test-row">3</div>
                              <div class="test-row">4</div>
                           </div>
                        </td>
                        <td>3,5</td>
                        <td>36</td>
                        <td>23</td>
                     </tr>
                     <tr>
                        <td>35,5</td>
                        <td>4</td>
                        <td>36⅔</td>
                        <td>23–23,5</td>
                     </tr>
                     <tr>
                        <td>36</td>
                        <td>4,5</td>
                        <td>37⅓</td>
                        <td>23,5</td>
                     </tr>
                     <tr>
                        <td>36,5</td>
                        <td>5</td>
                        <td>38</td>
                        <td>24</td>
                     </tr>
                     <tr>
                        <td>37</td>
                        <td>5,5</td>
                        <td>38⅔</td>
                        <td>24,5</td>
                     </tr>
                     </tbody>
                  </table>
                  <div class="navigation-table__footer">
                     <div class="navigation-table__pagination pagination">
                        <i class="pagination__arrow fas fa-chevron-left"></i>
                        <div class="pagination__label">1 из 5</div>
                        <i class="pagination__arrow fas fa-chevron-right"></i>
                     </div>
                  </div>
               </div>


               {{--<div class="test">
                  <div class="test__row">
                     <div class="test__col">123</div>
                     <div class="test__col">123</div>
                     <div class="test__col">123</div>
                     <div class="test__col">123</div>
                  </div>
                  <div class="test__row">
                     <div class="test__col">wwwwwwwwwwwwwwwwwwwwwwwwwwww</div>
                     <div class="test__col">123</div>
                     <div class="test__row">
                        <div class="test__col">456</div>
                        <div class="test__col">456</div>
                     </div>

                  </div>
               </div>--}}
{{--
               <div class="navigation__table navigation-table">
                  <div class="navigation-table__nav">
                     <div class="navigation-table__pagination pagination">
                        <i class="pagination__arrow fas fa-chevron-left"></i>
                        <div class="pagination__label">1 из 5</div>
                        <i class="pagination__arrow fas fa-chevron-right"></i>
                     </div>
                     <div class="navigation-table__size">
                        <div class="navigation-table__amount">25</div>
                        <div class="navigation-table__amount">50</div>
                        <div class="navigation-table__amount">75</div>
                     </div>
                     <div class="navigation-table__sort">
                        <div class="navigation-table__category">
                           <div class="navigation-table__category-label">id-заявления</div>
                           <i class="navigation-table__sort-icon fas fa-caret-up"></i>
                        </div>
                     </div>
                  </div>


                  <div class="navigation-table__body">
                     <div class="navigation-table__row">row1</div>
                     <div class="navigation-table__row">row2</div>
                  </div>
                  <div class="navigation-table__footer">footer</div>
               </div>
               --}}

            </div>
         </div>
      </div>
   </div>

   @include('components.footer')
@endsection

