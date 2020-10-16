<template>
<div>
    <fieldset>
        <legend>price</legend>
        {{start}}
        <input type="date" v-model="start" :min="min" :max="max">
        {{end}}
        <input type="date" v-model="end" :min="min" :max="max">
    </fieldset>
    <fieldset>
        <legend>Customer</legend>
        <select class="form-control" v-model="kode">
            <option :value="cs.kode" v-for="cs in customer">{{cs.nama}}</option>
        </select>
        <br>
    </fieldset>
    <fieldset>
        <legend>Nomor SO</legend>
        <input type="text" class="form-control" v-model="nomorso">
    </fieldset>

    <ul>
        <table class="table table-bordered">
            <thead>
                <th>No</th>
                <th>Nomor SO</th>
                <th>Tanggal</th>
                <th>Customer</th>
            </thead>
            <tbody v-for="(list,index) in filterList" :key="index">
                <td>{{index+1}}</td>
                <td>{{list.nomor_so}}</td>
                <td>{{list.tanggal_so}}</td>
                <td>{{list.customer}}</td>
            </tbody>
        </table>
    </ul>
</div>
</template>

<script>
export default {
    data() {
        return {
            min: '',
            max: '',
            start: this.Datesebulan(),
            end: this.DateTime(),
            kode: '',
            size: '',
            so: [],
            customer: {},
            nomorso: ''

        }
    },
    computed: {
        filterList() {
            var vm = this,
                lists = vm.so
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal_so >= vm.start && query.tanggal_so <= vm.end,
                    nomorso = vm.nomorso ? (query.nomor_so.toLowerCase().includes(vm.nomorso.toLowerCase())) : true,
                    customer = vm.kode ? (query.kode_customer == vm.kode) : true;

                console.log(query)
                return tanggal && nomorso && customer
                //  && color && size
            })
        }
    },
    mounted() {
        // var vm = this,
        //     lists = vm.so,
        //     max = _.maxBy(lists, 'price').price,
        //     min = _.minBy(lists, 'price').price
        // vm.start = min
        // vm.end = max
        // vm.min = min
        // vm.max = max
    },
    created() {
        this.getso();
    },
    methods: {
        getso() {
            axios.get("/api/so")
                .then(res => {
                    this.so = res.data.data;
                    axios.get("/api/customer")
                        .then(res => {
                            this.customer = res.data.data;
                        })
                })
        },
        DateTime() {
            this.date = new Date();
            this.month = this.date.getMonth() + 1;
            this.year = this.date.getFullYear();
            this.hours = this.date.getHours();
            this.minute = this.date.getMinutes();
            this.seconds = this.date.getSeconds();
            if (this.month > 12) {
                this.month = 12;
            }
            this.day = this.date.getDate();
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates + " " + this.times;
            return this.datetimes;
        },
        Datesebulan() {
            this.date = new Date();
            this.month = this.date.getMonth();
            this.year = this.date.getFullYear();
            this.hours = this.date.getHours();
            this.minute = this.date.getMinutes();
            this.seconds = this.date.getSeconds();
            if (this.month > 12) {
                this.month = 12;
            }
            this.day = this.date.getDate();
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates + " " + this.times;
            return this.datetimes;
        },
    }
}
</script>
