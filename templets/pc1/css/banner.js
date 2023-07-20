// JavaScript Document
(function(A) {
	A.fn.extend({
		"banner": function(B) {
			var C = {
				eml: ".page,.prev,.next,.title",
				direction: "lr",
				mode: "slide",
				pages: true,
				btns: true,
				title: true,
				autoanimate: true,
				ease: "easeInOutElastic",
				cycle: true,
				cycleType: true,
				auto: 2000,
				animation: 1000
			};
			var B = A.extend(C, B);
			return this.each(function() {
				var Q = B,
					I = A(this),
					T = I.find("li"),
					J = I.find(".page span"),
					P = I.find("li").length,
					R = I.find(".prev"),
					G = I.find(".next"),
					N = I.find(".title"),
					L = true;
				if (Q.direction == "ud" && Q.mode == "slide") {
					var K = I.find("ul");
					var F = K.find("li").outerWidth();
					var D = K.find("li").outerHeight();
					K.find("li").height(D);
					K.height(F * P);
					K.height(D)
				}
				if (Q.direction == "lr" && Q.mode == "slide") {
					var K = I.find("ul");
					var F = K.find("li").outerWidth();
					var D = K.find("li").outerHeight();
					K.find("li").width(F);
					K.width(F * P);
					K.height(D)
				}
				I.find(".cont").text(P);
				var O = "<div class='page'>";
				for (i = 1; i <= P; i++) {
					O += "<span></span>"
				}
				O += "</div>";
				I.append(O);
				var O = I.find(".page span");
				O.eq(0).addClass("current");
				var S = T.eq(0).find("img").attr("alt");
				I.find(".alt").text(S);
				if (Q.pages == false) {
					I.find(".page").hide()
				}
				if (Q.btns == false) {
					R.hide();
					G.hide()
				}
				if (Q.title == false) {
					N.hide()
				}
				if (Q.mode == "slide") {
					T.css({
						"float": "left"
					})
				} else {
					if (Q.mode == "fade") {
						T.css({
							"position": "absolute",
							"top": 0,
							"left": 0,
							"display": "none"
						});
						T.eq(0).show()
					}
				}
				if (Q.unlimited == true) {
					var H = 0;
					T.each(function() {
						A(this).attr("indexNum", H++)
					})
				}
				if (Q.cycle == true && Q.cycleType == true) {
					if (Q.direction == "ud" && Q.mode == "slide") {
						T.closest("ul").css({
							"position": "relative",
							"top": -D
						});
						T.css({
							"position": "absolute",
							"left": 0,
							"display": "none",
							"top": D,
							"z-index": 1
						});
						T.eq(0).css({
							"display": "block",
							"z-index": 5
						})
					} else {
						if (Q.direction == "lr" && Q.mode == "slide") {
							T.closest("ul").css({
								"position": "relative",
								"left": -F
							});
							T.css({
								"position": "absolute",
								"top": 0,
								"display": "none",
								"left": F,
								"z-index": 1
							});
							T.eq(0).css({
								"display": "block",
								"z-index": 5
							})
						}
					}
				}
				O.live("click", function(W) {
					var Z = O.index(this) + 1;
					S = T.eq(O.index(this)).find("img").attr("alt");
					I.find(".curr").text(Z);
					I.find(".alt").text(S);
					if (Q.direction == "ud" && Q.mode == "slide" && !K.is(":animated")) {
						if (Q.cycle == true && Q.cycleType == true) {
							var U = I.find(".page span.current").index();
							var Y = A(this).index();
							if (Y == P - 1 && U == 0) {
								L = false
							} else {
								if (Y == 0 && U == P - 1) {
									L = true
								} else {
									if (Y > U) {
										L = true
									} else {
										L = false
									}
								}
							}
							if (L) {
								K.css("top", -D);
								K.find("li").eq(A(this).index()).css({
									"top": D * 2,
									"display": "block"
								});
								K.stop(true, true).animate({
									"top": -D * 2
								}, Q.animation, Q.ease, function() {
									K.css("top", -D);
									K.find("li").eq(U).hide();
									K.find("li").eq(U).css({
										"z-index": 1
									});
									K.find("li").eq(Y).css({
										"z-index": 5,
										"top": D
									})
								})
							} else {
								K.css("top", -D);
								K.find("li").eq(A(this).index()).css({
									"top": 0,
									"display": "block"
								});
								K.stop(true, true).animate({
									"top": 0
								}, Q.animation, Q.ease, function() {
									K.css("top", -D);
									K.find("li").eq(U).hide();
									K.find("li").eq(U).css({
										"z-index": 1
									});
									K.find("li").eq(Y).css({
										"z-index": 5,
										"top": D
									})
								})
							}
						} else {
							K.stop(true, true).animate({
								marginTop: -D * (A(this).index())
							}, Q.animation, Q.ease)
						}
						A(this).addClass("current").siblings().removeClass("current")
					} else {
						if (Q.direction == "lr" && Q.mode == "slide" && !K.is(":animated")) {
							if (Q.cycle == true && Q.cycleType == true) {
								var V = I.find(".page span.current").index();
								var X = A(this).index();
								if (X == P - 1 && V == 0) {
									L = false
								} else {
									if (X == 0 && V == P - 1) {
										L = true
									} else {
										if (X > V) {
											L = true
										} else {
											L = false
										}
									}
								}
								if (L) {
									K.css("left", -F);
									K.find("li").eq(A(this).index()).css({
										"left": F * 2,
										"display": "block"
									});
									K.stop(true, true).animate({
										"left": -F * 2
									}, Q.animation, Q.ease, function() {
										K.css("left", -F);
										K.find("li").eq(V).hide();
										K.find("li").eq(V).css({
											"z-index": 1
										});
										K.find("li").eq(X).css({
											"z-index": 5,
											"left": F
										})
									})
								} else {
									K.css("left", -F);
									K.find("li").eq(A(this).index()).css({
										"left": 0,
										"display": "block"
									});
									K.stop(true, true).animate({
										"left": 0
									}, Q.animation, Q.ease, function() {
										K.css("left", -F);
										K.find("li").eq(V).hide();
										K.find("li").eq(V).css({
											"z-index": 1
										});
										K.find("li").eq(X).css({
											"z-index": 5,
											"left": F
										})
									})
								}
							} else {
								K.stop(true, true).animate({
									marginLeft: -F * (A(this).index())
								}, Q.animation, Q.ease)
							}
							A(this).addClass("current").siblings().removeClass("current")
						} else {
							if (Q.mode == "fade") {
								if (T.eq(O.index(this)).is(":hidden")) {
									T.stop(true, true).fadeOut(Q.animation).eq(O.removeClass("current").index(A(this).addClass("current"))).fadeIn(Q.animation)
								}
							}
						}
					}
				});
				if (Q.autoanimate == true) {
					var E = 1;
					var M = setInterval(function() {
						O.eq(E).click();
						E++;
						if (E == P) {
							E = 0
						}
					}, Q.auto);
					I.find(Q.eml).hover(function() {
						clearInterval(M)
					}, function() {
						E = I.find(".page span.current").index() + 1;
						if (E == P) {
							E = 0
						}
						M = setInterval(function() {
							O.eq(E).click();
							E++;
							if (E == P) {
								E = 0
							}
						}, Q.auto)
					})
				}
				R.click(function() {
					E = I.find(".page span.current").index() - 1;
					R.removeClass("disabled");
					G.removeClass("disabled");
					if (Q.cycle != true) {
						if (E == -1 || E == 0) {
							R.addClass("disabled")
						}
						if (E == -1) {
							return false
						}
					}
					O.eq(E).click()
				});
				G.click(function() {
					R.removeClass("disabled");
					G.removeClass("disabled");
					E = I.find(".page span.current").index() + 1;
					if (Q.cycle != true) {
						if (E == P || E == P - 1) {
							E = P - 1;
							if (E == P - 1 || E == P) {
								G.addClass("disabled")
							}
						}
					} else {
						if (E == P) {
							if (Q.cycle != true) {
								E = P - 1
							} else {
								E = 0
							}
						}
					}
					O.eq(E).click()
				})
			})
		}
	})
})(jQuery);