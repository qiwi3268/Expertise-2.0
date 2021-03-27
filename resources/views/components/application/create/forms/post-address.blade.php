<x-forms.inputs.postcode
    title="Почтовый индекс"
    name="postcode"
    required="true"
/>
<x-miscs.single
    title="Субъект Российской Федерации"
    required="true"
    alias="regionCode"
/>
<x-forms.inputs.input
    title="Наименование района"
    name="municipalDistrict"
    required="false"
/>
<x-forms.inputs.input
    title="Город"
    name="city"
    required="false"
/>
<x-forms.inputs.input
    title="Населенный пункт"
    name="locality"
    required="false"
/>
<x-forms.inputs.input
    title="Улица"
    name="street"
    required="false"
/>
<x-forms.inputs.input
    title="Номер здания/сооружения"
    name="building"
    required="false"
/>
<x-forms.inputs.input
    title="Номер помещения"
    name="room"
    required="false"
/>
<x-forms.inputs.text-area
    title="Дополнительные адресные данные"
    name="addressAdditionalInfo"
    required="false"
    maxLength="1000"
/>
