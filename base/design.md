# Model
- get,selectでフィールドを指定できる。
- 指定されなくても、主キーは必ず含まれる。
- validateでは指定したフィールドだけが検証される。
- updateでは指定したフィールドだけが更新される。

# ValModel
- モデル単位のバリデータ。
- create,select,update,その他カスタムメソッド用
# ValField
- フィールド種別単位のバリデータ。
    - ValPostal,ValEmail,ValPhoneNoなど
    - 