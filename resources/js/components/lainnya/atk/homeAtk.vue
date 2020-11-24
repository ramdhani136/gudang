<template>
  <div class="container">
    <div class="menu-atk">
      <select style="border: solid 1px #ccc; padding-left: 1%; color: #666" class="kategoriform">
        <option value="">Semua ATK</option>
        <option value="">Kertas</option>
        <option value="">Pulpen</option>
        <option value="">Penghapus</option>
      </select>
      <div class="count">21</div>
      <div @click="showdata()" class="cart">
        <img src="../../../../../public/img/icon/cart.png" alt="" />
      </div>
      <input style="width: 30%; float: right; border: solid 1px #ccc; padding-left: 1%; color: #666" type="text" class="kategoriform" placeholder="Pencarian ATK" />
    </div>
    <div class="konten">
      <div class="listitem">
        <img src="../../../../../public/img/atk/a4.jpg" alt="" />
        <b>Kertas A4</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/pulpenhitam.jpg" alt="" />
        <b>Pulpen Standard Hitam</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/penghapuskaret.jpg" alt="" />
        <b>Penghapus Karet</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/pgr.jpg" alt="" />
        <b>Penggaris Besi 30cm</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/2b.jpg" alt="" />
        <b>Pensil 2B</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/serut.jpg" alt="" />
        <b>Serutan Pensil</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/tip.jpg" alt="" />
        <b>Tip-Ex</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/stap.jpg" alt="" />
        <b>Stapler HD-10</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
      <div class="listitem">
        <img src="../../../../../public/img/atk/isi.jpg" alt="" />
        <b>Isi Staples</b>
        <div class="row">
          <button style="margin-top: 10px; margin-left: 22%; float: left" class="btn-sm btn-success">Lihat detail</button>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modal-data" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Data ATK</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Save Change</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["ambiluser"],
  components: {},
  data() {
    return {};
  },
  created() {
    this.showdata();
  },
  methods: {
    showdata() {
      $("#modal-data").modal("show");
    },
  },
};
</script>

<style>
.menu-atk {
  width: 100%;
  border: solid 1px rgb(228, 227, 227);
  padding: 1%;
}

.konten {
  width: 100%;
  height: 440px;
  margin-top: 15px;
  border: solid 1px #e5e6e7;
  overflow-y: scroll;
  background-color: #eff2f5;
  padding: 2%;
}

.kategoriform {
  width: 20%;
  height: 35px;
}

.listitem {
  border: solid 1px rgb(228, 227, 227);
  width: 15%;
  height: 260px;
  margin-right: 1.66%;
  float: left;
  margin-bottom: 15px;
  background-color: #fff;
  border-radius: 3px;
  padding: 1%;
}

.listitem img {
  width: 100%;
  height: 150px;
  border: solid 1px rgb(228, 227, 227);
  float: left;
}

.listitem b {
  float: left;
  margin-top: 10px;
  height: 35px;
  font-size: 0.9em;
  width: 100%;
  text-align: center;
}

.cart {
  width: 2.3%;
  height: 27px;
  float: right;
  margin-left: 1%;
  margin-top: 5px;
}

.cart img {
  width: 100%;
  height: 100%;
}

.count {
  position: relative;
  width: auto;
  height: auto;
  padding: 3px 0.6% 3px 0.6%;
  border: solid 1px rgb(207, 5, 5);
  float: right;
  border-radius: 50%;
  font-size: 0.6em;
  left: -1%;
  top: -3px;
  background-color: red;
  color: #fff;
  font-weight: bold;
}
</style>
